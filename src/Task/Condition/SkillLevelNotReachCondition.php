<?php

declare(strict_types=1);

namespace Application\Task\Condition;

use Application\Entity\Character;
use Application\Enum\SkillType;

class SkillLevelNotReachCondition implements Condition
{
    public function __construct(private readonly SkillType $skillType, private readonly int $level) {}

    public function isValid(Character $character): bool
    {
        return !$character->skills->hasLevel($this->skillType, $this->level);
    }

    public function render(Character $character): string
    {
        $level = $character->skills->getLevel($this->skillType);
        return "{$this->skillType->value} [$level/$this->level]";
    }
}
