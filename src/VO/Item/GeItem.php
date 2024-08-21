<?php

declare(strict_types=1);

namespace Application\VO\Item;

class GeItem
{
    public function __construct(
        public string $code,
        public int $stock,
        public int $sellPrice,
        public int $buyPrice,
    ) {}
}
