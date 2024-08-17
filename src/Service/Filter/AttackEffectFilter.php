<?php

declare(strict_types=1);

namespace Application\Service\Filter;

use Velkuns\ArtifactsMMO\VO\ItemEffect;

class AttackEffectFilter extends \FilterIterator
{
    /**
     * @param array<ItemEffect> $list
     */
    public function __construct(array $list)
    {
        parent::__construct(new \ArrayIterator($list));
    }

    public function accept(): bool
    {
        /** @var ItemEffect $effect */
        $effect = parent::current();

        return \str_starts_with($effect->name, 'attack_');
    }
}
