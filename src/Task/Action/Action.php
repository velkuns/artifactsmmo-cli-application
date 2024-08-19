<?php

declare(strict_types=1);

namespace Application\Task\Action;

use Application\Entity\Character;
use Application\Infrastructure\Helper\ApiErrorTrait;

abstract class Action
{
    use ApiErrorTrait;

    /**
     * @param array<mixed> $arguments
     * @param array<mixed> $argumentsForRepeatableCondition
     */
    public function __construct(
        protected readonly \Closure $callable,
        protected readonly array $arguments,
        protected readonly \Closure|null $repeatableCondition = null,
        protected readonly array $argumentsForRepeatableCondition = [],
    ) {}

    /**
     * @throws \Throwable
     */
    public function execute(): mixed
    {
        $this->render();

        try {
            $func = $this->callable;
            return $func(...$this->arguments);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    public function simulate(): bool
    {
        $this->render();

        return true;
    }

    public function repeatable(Character $character): bool
    {
        if ($this->repeatableCondition === null) {
            return false;
        }

        $func   = $this->repeatableCondition;
        $result = $func($character, ...$this->argumentsForRepeatableCondition);

        return (bool) $result;
    }

    private function render(): void
    {
        $actionName = \basename(\str_replace('\\', '/', $this::class));
        $args       = !empty($this->arguments) ? '(with args: ' . json_encode($this->arguments) . ')' : '';
        echo "> $actionName $args\n";
    }
}
