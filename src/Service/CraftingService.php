<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\VO\Item\Item;
use Application\VO\Position;

class CraftingService
{
    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly Command\CommandFactory $commandFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createCraftingCommands(Character $character, string $resource, int $quantity): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        $item = $this->itemRepository->findItem(Item::class, $code);

        if ($item === null || $item->craft === null) {
            return $commands;
        }

        $skillType = Skill
        $skillType =

        $nearest = null;
        $minDistance = 0;
        foreach ($maps as $map) {
            $distance = $this->distance($character->position, new Position($map->x, $map->y));
            if ($nearest === null || $distance < $minDistance) {
                $nearest     = new Position($map->x, $map->y);
                $minDistance = $distance;
            }
        }

        $commands = $this->commandFactory->newList();
        if ($minDistance > 0 && $nearest !== null) {
            $command = $this->commandFactory->new(Command\Move::class, $character->move(...), [$nearest]);
            $commands->unshift($command);
        }

        for ($i = 0; $i < $quantity; $i++) {
            $command = $this->commandFactory->new(Command\Gather::class, $character->gather(...), []);
            $commands->unshift($command);
        }

        return $commands;
    }

    private function distance(Position $characterPos, Position $resourcePos): int
    {
        return abs($resourcePos->x - $characterPos->x) + abs($resourcePos->y - $characterPos->y);
    }
}
