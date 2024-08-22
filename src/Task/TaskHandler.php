<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Task\Action\Action;
use Application\Entity\Character;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class TaskHandler
{
    public function __construct(
        private readonly ActionHandler $actionHandler,
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

            $character = $this->actionHandler->handle($character, $action, $simulate);

            $task->next();
        }
    }
}
