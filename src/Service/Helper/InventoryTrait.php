<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Entity\Character;

trait InventoryTrait
{
    protected function hasEnoughItem(Character $character, string $resource, int $quantity): bool
    {
        return $character->inventory->countItem($resource) < $quantity;
    }
}
