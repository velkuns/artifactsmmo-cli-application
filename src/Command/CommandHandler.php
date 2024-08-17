<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Entity\Character;
use Application\Infrastructure\Client\CharacterRepository;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class CommandHandler
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
    ) {}

    /**
     * @throws \Throwable
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handleList(Character $character, CommandList $commandList): void
    {
        $commandList->rewind();

        while ($commandList->valid()) {
            $character = $this->handle($character, $commandList->current());
            $commandList->next();
        }
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Command $command): Character
    {
        $character->waitForCooldown();

        $command->execute();

        //~ Update character after command execution
        return $this->characterRepository->findByName($character->name);
    }

}
