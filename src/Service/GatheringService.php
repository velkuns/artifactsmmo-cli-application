<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\VO\Position;

class GatheringService
{
    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly Command\CommandFactory $commandFactory,
    ) {}

    /**
     * @return list<Command\Command>
     * @throws \Throwable
     */
    public function createGatheringCommands(Character $character, string $resource, int $quantity): array
    {
        $maps = $this->mapRepository->findResource($resource);

        $nearest = null;
        $minDistance = 0;
        foreach ($maps as $map) {
            $distance = $this->distance($character->position, new Position($map->x, $map->y));
            if ($nearest === null || $distance < $minDistance) {
                $nearest     = $map;
                $minDistance = $distance;
            }
        }

        $commands = [];
        if ($minDistance > 0 && $nearest !== null) {
            $commands[] = $this->commandFactory->new(Command\Move::class, $character->move(...), [$nearest], $character);
        }

        return \array_merge(
            $commands,
            \array_fill(
                0,
                $quantity,
                $this->commandFactory->new(Command\Gather::class, $character->gather(...), [], $character),
            ),
        );
    }

    private function distance(Position $characterPos, Position $resourcePos): int
    {
        return abs($resourcePos->x - $characterPos->x) + abs($resourcePos->y - $characterPos->y);
    }
}
