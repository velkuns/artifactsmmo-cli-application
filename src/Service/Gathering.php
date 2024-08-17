<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Command\Action;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;

class Gathering
{
    use MapTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly Command\CommandFactory $commandFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createCommands(Character $character, string $resource, int $quantity): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findResource($resource), $commands);

        //~ Then enqueue main action
        for ($i = 0; $i < $quantity; $i++) {
            $command = $this->commandFactory->new(Action\Gather::class, $character->gather(...), []);
            $commands->enqueue($command);
        }

        return $commands;
    }

}
