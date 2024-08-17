<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Command\Action\Action;
use Application\Entity\Character;
use Application\Exception\CooldownException;
use Application\Infrastructure\Client\CharacterRepository;
use Application\Service\Waiter;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class CommandHandler
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly Waiter $waiter,
    ) {}

    /**
     * @throws \Throwable
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handleList(Character $character, CommandList $commandList, bool $simulate = false): void
    {
        $commandList->rewind();

        while ($commandList->valid()) {
            try {
                $character = $this->handle($character, $commandList->current(), $simulate);
                $commandList->next();
            } catch (CoolDownException $exception) {
                $this->waiter->wait($exception->getCooldownAsInt());
            }
        }
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Action $command, bool $simulate): Character
    {
        $character->waitForCooldown();

        //~ Update character after command execution
        if ($simulate) {
            $command->simulate();
            return $character;
        } else {
            $command->execute();
            $character = $this->characterRepository->findByName($character->name);
        }

        return $character;
    }
}
