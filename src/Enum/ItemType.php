<?php

declare(strict_types=1);

namespace Application\Enum;

enum ItemType: string
{
    case Weapon = 'weapon';
    case Shield = 'shield';
    case Helmet = 'helmet';
    case BodyArmor = 'body_armor';
    case LegArmor = 'leg_armor';
    case Boots = 'boots';
    case Ring = 'ring';
    case Amulet = 'amulet';
    case Artifact = 'artifact';
    case Consumable = 'consumable';

    /**
     * @return Slot|array<int, Slot>
     */
    public function slot(): Slot|array
    {
        return match($this) {
            self::Ring => [1 => Slot::Ring1, 2 => Slot::Ring2],
            self::Artifact => [1 => Slot::Artifact1, 2 => Slot::Artifact2, 3 => Slot::Artifact3],
            self::Consumable => [1 => Slot::Consumable1, 2 => Slot::Consumable2],
            default => Slot::from($this->value),
        };
    }
}
