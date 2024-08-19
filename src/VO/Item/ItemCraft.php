<?php

declare(strict_types=1);

namespace Application\VO\Item;

use Velkuns\ArtifactsMMO\VO\SimpleItem;

class ItemCraft
{
    /**
     * @param string $skill
     * @param int $level
     * @param int $quantity
     * @param SimpleItem[] $items
     */
    public function __construct(
        public readonly string $skill,
        public readonly int $level,
        public readonly int $quantity,
        public readonly array $items,
    ) {}
}
