<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Task\Action\Action;
use Application\Entity\Character;
use Application\Exception\CooldownException;
use Application\Service\Waiter;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class TaskHandler
{
    public function __construct(
        private readonly ActionHandler $actionHandler,
        private readonly Waiter $waiter,
    ) {}

    /**
     * @throws \Throwable
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Task $task, bool $simulate = false): void
    {
        $task->rewind();

        while (!$task->isEmpty()) {
            /** @var Action $action */
            $action = $task->dequeue();

            do {
                $retryAfterCooldown = false;
                try {
                    $character = $this->actionHandler->handle($character, $action, $simulate);
                } catch (CoolDownException $exception) {
                    echo "Cooldown not finished (remaining time: {$exception->getCooldown()}s)!\n";
                    $this->waiter->wait($exception->getCooldownAsInt());
                    $retryAfterCooldown = true;
                }
            } while ($retryAfterCooldown || ($action->repeatable($character) && $simulate === false));

            if ($simulate && $action->repeatable($character)) {
                echo " --> No repeatable action due to simulation mode [ON].\n";
            }

            $task->next();
        }
    }
}
