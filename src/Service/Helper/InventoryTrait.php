<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Entity\Character;

trait InventoryTrait
{
    protected function countItemInInventory(Character $character, string $code): int
    {
        return $character->inventory->countItem($code);
    }
}
