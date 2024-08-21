<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Entity\Character;
use Application\Service\Renderer\ObjectiveRenderer;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class ObjectiveHandler
{
    public function __construct(
        private readonly TaskHandler $taskHandler,
        private readonly ObjectiveRenderer $renderer,
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
        $this->renderer->displaySubTitle('Doing task for objective' . ($simulate ? ' - SIMULATION' : ''));
        while (!$objective->isEmpty()) {
            $task = $objective->dequeue();
            $this->taskHandler->handle($character, $task, $simulate);
        }
    }
}
