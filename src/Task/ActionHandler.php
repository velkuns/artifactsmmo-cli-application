<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Task\Action\Action;
use Application\Entity\Character;
use Application\Infrastructure\Client\CharacterRepository;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class ActionHandler
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
    ) {}

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Action $action, bool $simulate): Character
    {
        $character->waitForCooldown();

        //~ Update character after action execution
        if ($simulate) {
            $action->simulate();
            return $character;
        } else {
            $action->execute();
            $character = $this->characterRepository->findByName($character->name);
        }

        return $character;
    }
}
