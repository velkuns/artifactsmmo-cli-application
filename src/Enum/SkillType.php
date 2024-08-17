<?php

declare(strict_types=1);

namespace Application\Enum;

use Application\Command;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\VO\Position;

enum SkillType: string
{
    case Cooking = 'cooking';
    case Mining = 'mining';
    case WoodCutting = 'woodcutting';
    case WeaponCrafting = 'weaponcrafting';
    case GearCrafting = 'gearcrafting';
    case JewelCrafting = 'jewelcrafting';

    /**
     * @return array{type: 'workshop', code: string}
     */
    public function workshop(): array
    {
        return ['type' => 'workshop', 'code' => $this->value];
    }
}
