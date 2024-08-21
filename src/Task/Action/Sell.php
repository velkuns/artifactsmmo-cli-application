<?php

declare(strict_types=1);

namespace Application\Task\Action;

use Application\VO\Item\GeItem;

class Sell extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /**
         * @var GeItem $item
         * @var int $quantity
         */
        [$item, $quantity] = $this->context;

        return " ▶ $state - Sell $item->code on GE [x$quantity] for ~$item->sellPrice 🪙 (each)";
    }
}
