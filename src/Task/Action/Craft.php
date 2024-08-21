<?php

declare(strict_types=1);

namespace Application\Task\Action;

class Craft extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /**
         * @var string $item
         * @var int $quantity
         */
        [$item, $quantity] = $this->arguments;
        return " ▶ $state - Crafting '$item' [x$quantity]";
    }
}
