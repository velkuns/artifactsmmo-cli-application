<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\VO\Position;

trait MathTrait
{
    private function distance(Position $characterPos, Position $resourcePos): int
    {
        return abs($resourcePos->x - $characterPos->x) + abs($resourcePos->y - $characterPos->y);
    }
}
