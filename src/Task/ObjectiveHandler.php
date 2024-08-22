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
        $repeatable = $objective->repeatable($character);

        do {
            $this->renderer->displaySubTitle('Doing task for objective' . ($repeatable ? ' 🔁 ' : '') . ($simulate ? ' - SIMULATION' : ''));

            $repeatObjective = $objective->new();
            while (!$objective->isEmpty()) {
                $task = $objective->dequeue();
                $repeatObjective->enqueue(clone $task);

                $this->taskHandler->handle($character, $task, $simulate);
            }
            $objective = $repeatObjective;
        } while ($objective->repeatable($character) && $simulate === false);
    }

    /**
     * @throws \Throwable
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handleMultiple(Character $character, Objectives $objectives, bool $simulate): void
    {
        while (!$objectives->isEmpty()) {
            $objective = $objectives->dequeue();
            $this->handle($character, $objective, $simulate);
        }
    }
}
