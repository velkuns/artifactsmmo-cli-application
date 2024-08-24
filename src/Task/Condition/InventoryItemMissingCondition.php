<?php

declare(strict_types=1);

namespace Application\Task\Condition;

use Application\Entity\Character;

class InventoryItemMissingCondition implements Condition
{
    public function __construct(private readonly string $code, private readonly int $quantity) {}

    public function isValid(Character $character): bool
    {
        $count = $character->inventory->countItem($this->code);
        return $count < $this->quantity;
    }

    public function render(Character $character): string
    {
        $count = $character->inventory->countItem($this->code);
        return "[$count/$this->quantity]";
    }
}
