<?php

declare(strict_types=1);

namespace Application\VO\Equipment;

use Application\VO\Item;

class Jewels
{
    /**
     * @param array{1: Item\Ring|null, 2: Item\Ring|null} $rings
     */
    public function __construct(
        public array $rings,
        public Item\Amulet|null $amulet,
    ) {}
}
