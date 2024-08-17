<?php

declare(strict_types=1);

namespace Application\VO;

class Elements
{
    public function __construct(
        public int $fire,
        public int $earth,
        public int $water,
        public int $air,
    ) {}
}
