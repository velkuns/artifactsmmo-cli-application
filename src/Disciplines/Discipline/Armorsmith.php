<?php

declare(strict_types=1);

namespace Application\Disciplines\Discipline;

use Application\Disciplines\CraftingDiscipline;
use Application\Disciplines\Discipline;
use Application\Enum\SkillType;

class Armorsmith implements Discipline, CraftingDiscipline
{
    public function getName(): string
    {
        return 'Armorsmith';
    }

    public function getSkillType(): SkillType
    {
        return SkillType::GearCrafting;
    }
}
