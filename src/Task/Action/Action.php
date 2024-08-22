<?php

declare(strict_types=1);

namespace Application\Task\Action;

use Application\Entity\Character;
use Application\Infrastructure\Client\CharacterRepository;
use Application\Service\Waiter;
use Application\Task\Condition\Condition;
use Eureka\Component\Console\Terminal\Terminal;
use Velkuns\ArtifactsMMO\Exception\Api\CharacterAlreadyAtDestinationException;

abstract class Action
{
    /**
     * @param array<mixed> $arguments
     * @param array<mixed> $context Context for rendering
     */
    public function __construct(
        protected readonly Terminal $terminal,
        protected readonly Waiter $waiter,
        protected readonly CharacterRepository $characterRepository,
        protected readonly \Closure $callable,
        protected readonly array $arguments = [],
        protected readonly Condition|null $repeatableCondition = null,
        protected readonly array $context = [],
    ) {}

    public function isSkippable(Character $character): bool
    {
        return false;
    }

    /**
     * @throws \Throwable
     */
    public function execute(Character $character, bool $simulate): Character
    {
        $output = $this->terminal->output();

        if ($simulate) {
            $output->writeln($this->render($character, '✅'));
            return $character;
        }

        if ($this->isSkippable($character)) {
            $output->writeln($this->render($character, '⏩'));
            return $character;
        }

        //~ Wait for any previous cooldown
        $this->cooldown($character, ' ⏸ Character not yet available ...');
        do {
            try {
                $text = $this->render($character);
                $output->write("$text\r");
                $func = $this->callable;
                $func(...$this->arguments);

                $text = $this->render($character, '⌛');
                $output->write("$text\r");

                $character = $this->characterRepository->findByName($character->name);
                $this->cooldown($character, $text, "\r");

                //~ Be sure we have character up to date
                $character = $this->characterRepository->findByName($character->name);
                $text = $this->render($character, '✅');
                $output->write("$text\r");

            } catch (CharacterAlreadyAtDestinationException) {
                $text = $this->render($character, '⏩');
                $output->write("$text\r");
                continue;
            } catch (\Throwable $exception) {
                $output->writeln($this->render($character, '❎'));
                throw $exception;
            }
        } while ($this->repeatable($character));

        $output->writeln('');

        return $character;
    }

    public function repeatable(Character $character): bool
    {
        if ($this->repeatableCondition === null) {
            return false;
        }

        return $this->repeatableCondition->isValid($character);
    }

    protected function cooldown(Character $character, string $label, string $endLine = \PHP_EOL): void
    {
        $this->waiter->waitForCooldown($character, "$label -", $endLine);
    }

    private function render(Character $character, string $state = '🆕'): string
    {
        return $this->renderAction($state) . $this->renderRepeatable($character);
    }

    protected function renderAction(string $state): string
    {
        $actionName = \basename(\str_replace('\\', '/', $this::class));
        $args       = !empty($this->arguments) ? '(with args: ' . json_encode($this->arguments) . ')' : '';
        return " ▶ $state - $actionName $args";
    }

    protected function renderRepeatable(Character $character): string
    {
        $repeatableLabel = $this->repeatableCondition?->render($character) ?? '';

        return $repeatableLabel !== '' ? " - $repeatableLabel 🔁" : '';
    }
}
