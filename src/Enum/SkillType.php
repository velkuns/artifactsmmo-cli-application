<?php

declare(strict_types=1);

namespace Application\Enum;

enum SkillType: string
{
    case Combat = 'combat';
    case Cooking = 'cooking';
    case Mining = 'mining';
    case Fishing = 'fishing';
    case WoodCutting = 'woodcutting';
    case WeaponCrafting = 'weaponcrafting';
    case GearCrafting = 'gearcrafting';
    case JewelryCrafting = 'jewelrycrafting';
}
