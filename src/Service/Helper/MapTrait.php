<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Task\Action;
use Application\Task\Task;
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
    private function handleMove(Character $character, array $maps, Task $task): Task
    {
        [$position, $minDistance] = $this->findNearest($character, $maps);

        if ($minDistance > 0 && $position !== null) {
            $action = $this->actionFactory->new(Action\Move::class, $character->move(...), [$position]);
            $task->enqueue($action);
        }

        return $task;
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
