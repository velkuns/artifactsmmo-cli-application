<?php

declare(strict_types=1);

namespace Application\Service\Renderer;

use Application\Entity\Character;
use Application\VO\Elements;
use Application\VO\Item\Item;
use Application\VO\Item\Weapon;
use Application\VO\Skill;
use Application\VO\Skills;
use Eureka\Component\Console\Option\Options;
use Eureka\Component\Console\Progress\ProgressBar;

class ItemRenderer
{
    public function renderAll(array $items): string
    {
        $buffer = '';
        foreach ($items as $item) {
            $buffer .= $this->render($item);
        }

        return $buffer;
    }

    public function render(Item $item): string
    {
        return
            "--------------------------------------------------\n" .
            "Name: $item->name - $item->code\n" .
            "Description: $item->description\n" .
            "--------------------------------------------------\n" .
            "$item->hp ❤️ - $item->haste 💨\n" .
            "Attack:     {$this->element($item->attack)}\n" .
            "Resistance: {$this->element($item->resistance, '%')}\n" .
            "Damage:     {$this->element($item->damage, '%')}\n" .
            "--------------------------------------------------\n" .
            "Craft ({$item->craft->skill}): \n" .
            " . lvl: {$item->craft->level}\n" .
            " . qty: {$item->craft->level}\n" .
            " . items: [...]\n" .
            "--------------------------------------------------\n"
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
}
