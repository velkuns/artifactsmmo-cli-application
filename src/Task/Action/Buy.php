<?php

declare(strict_types=1);

namespace Application\Task\Action;

use Application\VO\Item\GeItem;

class Buy extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /**
         * @var GeItem $item
         * @var int $quantity
         */
        [$item, $quantity] = $this->context;

        return " ▶ $state - Buy $item->code on GE [x$quantity] for ~$item->buyPrice 🪙 (each)";
    }
}
