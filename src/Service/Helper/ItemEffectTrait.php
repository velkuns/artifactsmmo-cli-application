<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Service\Filter\AttackEffectFilter;
use Application\Service\Filter\DamageEffectFilter;
use Application\Service\Filter\HasteEffectFilter;
use Application\Service\Filter\HpEffectFilter;
use Application\Service\Filter\ResistanceEffectFilter;
use Application\VO\Effect\Attack;
use Application\VO\Effect\Damage;
use Application\VO\Effect\Resistance;
use Velkuns\ArtifactsMMO\VO\ItemEffect;

trait ItemEffectTrait
{
    /**
     * @param array<ItemEffect> $itemEffects
     */
    protected function getHpEffect(array $itemEffects): int
    {
        $hp = 0;

        /** @var ItemEffect $itemEffect */
        foreach (new HpEffectFilter($itemEffects) as $itemEffect) {
            $hp += (int) $itemEffect->value;
        }

        return $hp;
    }
    /**
     * @param array<ItemEffect> $itemEffects
     */
    protected function getHasteEffect(array $itemEffects): int
    {
        $haste = 0;

        /** @var ItemEffect $itemEffect */
        foreach (new HasteEffectFilter($itemEffects) as $itemEffect) {
            $haste += (int) $itemEffect->value;
        }

        return $haste;
    }

    /**
     * @param array<ItemEffect> $itemEffects
     */
    protected function getAttackEffects(array $itemEffects): Attack
    {
        $effects = ['fire' => 0, 'earth' => 0, 'water' => 0, 'air' => 0];

        /** @var ItemEffect $itemEffect */
        foreach (new AttackEffectFilter($itemEffects) as $itemEffect) {
            [, $type] = \explode('_', $itemEffect->name);
            $effects[$type] = $itemEffect->value;
        }

        return new Attack($effects['fire'], $effects['earth'], $effects['water'], $effects['air']);
    }

    /**
     * @param array<ItemEffect> $itemEffects
     */
    protected function getDamageEffects(array $itemEffects): Damage
    {
        $effects = ['fire' => 0, 'earth' => 0, 'water' => 0, 'air' => 0];

        /** @var ItemEffect $itemEffect */
        foreach (new DamageEffectFilter($itemEffects) as $itemEffect) {
            [, $type] = \explode('_', $itemEffect->name);
            $effects[$type] = $itemEffect->value;
        }

        return new Damage($effects['fire'], $effects['earth'], $effects['water'], $effects['air']);
    }

    /**
     * @param array<ItemEffect> $itemEffects
     */
    protected function getResistanceEffects(array $itemEffects): Resistance
    {
        $effects = ['fire' => 0, 'earth' => 0, 'water' => 0, 'air' => 0];

        /** @var ItemEffect $itemEffect */
        foreach (new ResistanceEffectFilter($itemEffects) as $itemEffect) {
            [, $type] = \explode('_', $itemEffect->name);
            $effects[$type] = $itemEffect->value;
        }

        return new Resistance($effects['fire'], $effects['earth'], $effects['water'], $effects['air']);
    }
}
