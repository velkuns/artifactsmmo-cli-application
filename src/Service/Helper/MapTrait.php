<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Command\Action;
use Application\Command\CommandList;
use Application\Entity\Character;
use Application\VO\Position;
use Velkuns\ArtifactsMMO\VO\Map;

trait MapTrait
{
    use MathTrait;

    /**
     * @param Map[] $maps
     * @throws \Exception
     */
    private function handleMove(Character $character, array $maps, CommandList $commands): CommandList
    {
        [$position, $minDistance] = $this->findNearest($character, $maps);

        if ($minDistance > 0 && $position !== null) {
            $command = $this->commandFactory->new(Action\Move::class, $character->move(...), [$position]);
            $commands->enqueue($command);
        }

        return $commands;
    }

    /**
     * @param Map[] $maps
     * @return array{0: Position|null, 1: int}
     * @throws \Exception
     */
    private function findNearest(Character $character, array $maps): array
    {
        $nearest     = null;
        $minDistance = 0;
        foreach ($maps as $map) {
            $distance = $this->distance($character->position, new Position($map->x, $map->y));
            if ($nearest === null || $distance < $minDistance) {
                $nearest     = new Position($map->x, $map->y);
                $minDistance = $distance;
            }
        }

        return [$nearest, $minDistance];
    }
}
