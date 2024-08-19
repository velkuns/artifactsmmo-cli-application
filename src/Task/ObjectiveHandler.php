<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Entity\Character;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class ObjectiveHandler
{
    public function __construct(
        private readonly TaskHandler $taskHandler,
    ) {}

    /**
     * @throws \Throwable
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Objective $objective, bool $simulate): void
    {
        while (!$objective->isEmpty()) {
            $task = $objective->dequeue();
            $this->taskHandler->handle($character, $task, $simulate);
        }
    }
}
