<?php

declare(strict_types=1);

namespace Application\Disciplines\Discipline;

use Application\Disciplines\Discipline;
use Application\Disciplines\GatheringDiscipline;
use Application\Enum\SkillType;

class Lumberjack implements Discipline, GatheringDiscipline
{
    public function getName(): string
    {
        return 'Lumberjack';
    }

    public function getSkillType(): SkillType
    {
        return SkillType::WoodCutting;
    }
}
