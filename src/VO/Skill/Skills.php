<?php

declare(strict_types=1);

namespace Application\VO\Skill;

use Application\Enum\SkillType;

class Skills
{
    public Skill $combat;
    public Skill $cooking;
    public Skill $fishing;
    public Skill $mining;
    public Skill $woodCutting;
    public Skill $weaponCrafting;
    public Skill $gearCrafting;
    public Skill $jewelryCrafting;

    public function hasLevel(SkillType $skillType, int $level): bool
    {
        return match ($skillType) {
            SkillType::Cooking         => $this->cooking->level >= $level,
            SkillType::Fishing         => $this->fishing->level >= $level,
            SkillType::Mining          => $this->mining->level >= $level,
            SkillType::WoodCutting     => $this->woodCutting->level >= $level,
            SkillType::WeaponCrafting  => $this->weaponCrafting->level >= $level,
            SkillType::GearCrafting    => $this->gearCrafting->level >= $level,
            SkillType::JewelryCrafting => $this->jewelryCrafting->level >= $level,
        };
    }
}
