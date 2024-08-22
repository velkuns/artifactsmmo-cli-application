<?php

declare(strict_types=1);

namespace Application\Disciplines\Discipline;

use Application\Disciplines\Discipline;
use Application\Disciplines\GatheringDiscipline;
use Application\Enum\SkillType;

class Miner implements Discipline, GatheringDiscipline
{
    public function getName(): string
    {
        return 'Miner';
    }

    public function getSkillType(): SkillType
    {
        return SkillType::Mining;
    }
}
