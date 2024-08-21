<?php

declare(strict_types=1);

namespace Application\Task\Action;

use Application\Entity\Character;
use Application\VO\Position;
use Velkuns\ArtifactsMMO\VO\Map;

class Move extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /** @var Map $map */
        [$map] = $this->context;

        $location = $map->content === null ? $map->name : ucfirst($map->content->type) . " ({$map->content->code})";

        $position = $this->getDestination();
        return " ▶ $state - Moving to $location in ($position->x, $position->y)";
    }

    private function getDestination(): Position
    {
        /** @var Position $position */
        $position = $this->arguments[0];

        return $position;
    }
}
