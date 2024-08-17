<?php

declare(strict_types=1);

namespace Application\VO;

use Velkuns\ArtifactsMMO\VO\InventorySlot;

class Inventory
{
    /**
     * @param InventorySlot[] $inventory
     */
    public function __construct(public array $inventory, public int $size) {}
}
