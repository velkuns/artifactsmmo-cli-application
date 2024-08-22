<?php

declare(strict_types=1);

namespace Application\Disciplines;

use Application\Enum\SkillType;

interface Discipline
{
    public function getName(): string;

    public function getSkillType(): SkillType;
}
