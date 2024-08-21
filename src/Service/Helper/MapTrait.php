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
        $map = $this->findNearest($character, $maps);

        $action = $this->actionFactory->move($character, new Position($map->x, $map->y), $map);
        $task->enqueue($action);

        return $task;
    }

    /**
     * @param Map[] $maps
     * @throws \Exception
     */
    private function findNearest(Character $character, array $maps): Map
    {
        $nearest     = null;
        $minDistance = 0;
        foreach ($maps as $map) {
            $distance = $this->distance($character->position, new Position($map->x, $map->y));
            if ($nearest === null || $distance < $minDistance) {
                $nearest     = $map;
                $minDistance = $distance;
            }
        }

        if ($nearest === null) {
            throw new \UnexpectedValueException('No nearest map found');
        }

        return $nearest;
    }
}
