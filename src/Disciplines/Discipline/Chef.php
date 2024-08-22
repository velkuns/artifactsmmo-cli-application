<?php

declare(strict_types=1);

namespace Application\Disciplines\Discipline;

use Application\Disciplines\CraftingDiscipline;
use Application\Disciplines\Discipline;
use Application\Enum\SkillType;

class Chef implements Discipline, CraftingDiscipline
{
    public function getName(): string
    {
        return 'Chef';
    }

    public function getSkillType(): SkillType
    {
        return SkillType::Cooking;
    }
}
