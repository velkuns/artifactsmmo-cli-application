<?php

declare(strict_types=1);

namespace Application\Disciplines\Discipline;

use Application\Disciplines\Discipline;
use Application\Disciplines\GatheringDiscipline;
use Application\Enum\SkillType;

class Fisher implements Discipline, GatheringDiscipline
{
    public function getName(): string
    {
        return 'Fisher';
    }

    public function getSkillType(): SkillType
    {
        return SkillType::Fishing;
    }
}
