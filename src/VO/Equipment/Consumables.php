<?php

declare(strict_types=1);

namespace Application\VO\Equipment;

use Application\VO\Item;

class Consumables
{
    /**
     * @param array{1: Item\Consumable|null, 2: Item\Consumable|null} $consumables
     */
    public function __construct(public array $consumables) {}
}
