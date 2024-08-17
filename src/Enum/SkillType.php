<?php

declare(strict_types=1);

namespace Application\Enum;

enum SkillType: string
{
    case Cooking = 'cooking';
    case Mining = 'mining';
    case WoodCutting = 'woodcutting';
    case WeaponCrafting = 'weaponcrafting';
    case GearCrafting = 'gearcrafting';
    case JewelryCrafting = 'jewelrycrafting';
}
