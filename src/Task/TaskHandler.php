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
            try {
                /** @var Action $action */
                $action = $task->dequeue();
                do {
                    $character = $this->actionHandler->handle($character, $action, $simulate);
                } while ($action->repeatable($character) && $simulate === false);

                if ($simulate === true) {
                    echo " --> No repeatable action due to simulation mode [ON].\n";
                }

                $task->next();
            } catch (CoolDownException $exception) {
                $this->waiter->wait($exception->getCooldownAsInt());
            }
        }
    }
}
