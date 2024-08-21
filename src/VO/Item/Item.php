<?php

declare(strict_types=1);

namespace Application\VO\Item;

use Application\Entity\Character;
use Application\Enum\SkillType;
use Application\VO\Effect\Attack;
use Application\VO\Effect\Damage;
use Application\VO\Effect\Resistance;

class Item
{
    public function __construct(
        public string $code,
        public string $name = '',
        public string $description = '',
        public string $type = '',
        public string $subType = '',
        public int $level = 0,
        public int $hp = 0,
        public int $haste = 0,
        public Attack|null $attack = null,
        public Resistance|null $resistance = null,
        public Damage|null $damage = null,
        public ItemCraft|null $craft = null,
    ) {}

    public function craftableBy(Character $character): bool
    {
        if ($this->craft === null) {
            return false;
        }

        try {
            $skillType = SkillType::from($this->craft->skill);
        } catch (\TypeError) {
            throw new \UnexpectedValueException("Skill type '{$this->craft->skill}' is not valid.");
        }

        if (!$character->skills->hasLevel($skillType, $this->craft->level)) {
            return false;
        }

        return true;
    }
}
