<?php

declare(strict_types=1);

namespace Application\Service\Renderer;

use Application\Disciplines\Disciplines;
use Application\Entity\Character;
use Application\VO\Elements;
use Application\VO\Item\Weapon;
use Application\VO\Skill\Skill;
use Application\VO\Skill\Skills;
use Eureka\Component\Console\Option\Options;
use Eureka\Component\Console\Progress\ProgressBar;

class CharacterRenderer
{
    public function render(Character $character): string
    {
        return
            "------------------------------------------------------------------------\n" .
            "Name: $character->name {$this->disciplines($character->disciplines)} - $character->hp ❤️ - 🗺️: ({$character->position->x}, {$character->position->y})\n" .
            "------------------------------------------------------------------------\n" .
            $this->skills($character->skills) .
            "-----------------------------------------\n" .
            "Attack:     {$this->element($character->attack)}\n" .
            "Resistance: {$this->element($character->resistance, '%')}\n" .
            "Damage:     {$this->element($character->damage, '%')}\n" .
            "-----------------------------------------\n" .
            "{$this->weapon($character->weapon)}\n"
        ;
    }

    public function element(Elements|null $elements, string $suffix = ' '): string
    {
        if ($elements === null) {
            return '';
        }

        return \implode(
            ' | ',
            [
                "$elements->fire$suffix 🔥",
                "$elements->earth$suffix 🌱",
                "$elements->water$suffix 🌊",
                "$elements->air$suffix 💨",
            ],
        );
    }

    public function weapon(Weapon|null $weapon): string
    {
        if ($weapon === null) {
            return "Weapon: None\n";
        }

        return
            "Weapon: $weapon->name\n" .
            " . level: $weapon->level\n" .
            " . attack: {$this->element($weapon->attack)}\n"
        ;
    }

    public function skills(Skills $skills): string
    {
        return
            "Skills:\n" .
            "⚔️: lvl {$skills->combat->level} - {$this->skillBar($skills->combat)}\n" .
            "⛏️: lvl {$skills->mining->level} - {$this->skillBar($skills->mining)}\n" .
            "🎣: lvl {$skills->fishing->level} - {$this->skillBar($skills->fishing)}\n" .
            "🍳: lvl {$skills->cooking->level} - {$this->skillBar($skills->cooking)}\n" .
            "🪓: lvl {$skills->woodCutting->level} - {$this->skillBar($skills->woodCutting)}\n" .
            "🔨: lvl {$skills->weaponCrafting->level} - {$this->skillBar($skills->weaponCrafting)}\n" .
            "⚙️: lvl {$skills->gearCrafting->level} - {$this->skillBar($skills->gearCrafting)}\n" .
            "💎: lvl {$skills->jewelryCrafting->level} - {$this->skillBar($skills->jewelryCrafting)}\n"
        ;
    }

    public function skillBar(Skill $skill): string
    {
        return (new ProgressBar(new Options(), $skill->maxXp))
            ->inc($skill->xp)
            ->render("$skill->xp / $skill->maxXp")
        ;
    }

    public function disciplines(Disciplines $disciplines): string
    {
        return "[{$disciplines->main->getName()}]";
    }
}
