<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\VO\Item\Amulet;
use Application\VO\Item\Artifact;
use Application\VO\Item\BodyArmor;
use Application\VO\Item\Boots;
use Application\VO\Item\Consumable;
use Application\VO\Item\Helmet;
use Application\VO\Item\Item;
use Application\VO\Item\ItemCraft;
use Application\VO\Item\LegArmor;
use Application\VO\Item\Ring;
use Application\VO\Item\Shield;
use Application\VO\Item\Weapon;
use Velkuns\ArtifactsMMO\VO\Craft;
use Velkuns\ArtifactsMMO\VO\Item as ItemClientVO;

trait ItemTrait
{
    /**
     * @template T
     * @param class-string<T> $itemClass
     * @return T
     */
    protected function newItem(string $itemClass, ItemClientVO $item)
    {
        $effects = $item->effects ?? [];

        return new $itemClass(
            $item->code,
            $item->name,
            $item->description,
            $item->type,
            $item->subtype,
            $item->level,
            $this->getHpEffect($effects),
            $this->getHasteEffect($effects),
            $this->getAttackEffects($effects),
            $this->getResistanceEffects($effects),
            $this->getDamageEffects($effects),
            $this->getItemCraft($item->craft ?? null),
        );
    }

    protected function newEmptyItem(string $code, Craft|null $craft): Item
    {
        return new Item($code, craft: $this->getItemCraft($craft));
    }

    protected function getItemCraft(Craft|null $craft): ItemCraft|null
    {
        if ($craft === null || !isset($craft->skill, $craft->level, $craft->quantity, $craft->items)) {
            return null;
        }

        return new ItemCraft(
            $craft->skill,
            $craft->level,
            $craft->quantity,
            $craft->items,
        );
    }

    /**
     * @return class-string<Item>
     */
    protected function getItemClass(string $type): string
    {
        return match($type) {
            'weapon' => Weapon::class,
            'shield' => Shield::class,
            'helmet' => Helmet::class,
            'body_armor' => BodyArmor::class,
            'leg_armor' => LegArmor::class,
            'boots' => Boots::class,
            'artifact' => Artifact::class,
            'ring' => Ring::class,
            'amulet' => Amulet::class,
            'consumable' => Consumable::class,
            default => Item::class,
        };
    }
}
