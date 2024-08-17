<?php

declare(strict_types=1);

namespace Application\Enum;

enum Slot: string
{
    case Weapon = 'weapon';
    case Shield = 'shield';
    case Helmet = 'helmet';
    case BodyArmor = 'body_armor';
    case LegArmor = 'leg_armor';
    case Boots = 'boots';
    case Ring1 = 'ring1';
    case Ring2 = 'ring2';
    case Amulet = 'amulet';
    case Artifact1 = 'artifact1';
    case Artifact2 = 'artifact2';
    case Artifact3 = 'artifact3';
    case Consumable1 = 'consumable1';
    case Consumable2 = 'consumable2';
}
