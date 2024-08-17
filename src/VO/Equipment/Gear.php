<?php

declare(strict_types=1);

namespace Application\VO\Equipment;

use Application\VO\Item;

class Gear
{
    public function __construct(
        public Item\Shield|null $shield,
        public Item\Helmet|null $helmet,
        public Item\BodyArmor|null $bodyArmor,
        public Item\LegArmor|null $legArmor,
        public Item\Boots|null $boots,
    ) {}
}
