<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Entity\Character;

trait InventoryTrait
{
    protected function isMissingItem(Character $character, string $code, int $quantity): bool
    {
        $count     = $character->inventory->countItem($code);
        $isMissing = $count < $quantity;
        echo "Is missing $code ? " . ($isMissing ? ' Yes' : 'No') . " (missing " . ($quantity - $count) . " $code) !\n";
        return $isMissing;
    }

    protected function countItemInInventory(Character $character, string $code): int
    {
        return $character->inventory->countItem($code);
    }
}
