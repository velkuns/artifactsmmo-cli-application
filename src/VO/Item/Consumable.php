<?php

declare(strict_types=1);

namespace Application\VO\Item;

class Consumable extends Item
{
    public string $type = 'consumable';
    public int $quantity = 1;
}
