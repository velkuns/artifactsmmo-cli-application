<?php

declare(strict_types=1);

namespace Application\VO;

class Position
{
    public function __construct(public int $x, public int $y) {}
}
