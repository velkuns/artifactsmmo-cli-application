<?php

declare(strict_types=1);

namespace Application\VO;

class Skill
{
    public function __construct(
        public int $level,
        public int $xp,
        public int $maxXp,
        public int $totalXp = 0,
    ) {}
}
