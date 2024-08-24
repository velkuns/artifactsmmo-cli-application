<?php

declare(strict_types=1);

namespace Application\Task\Action;

class Fight extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /**
         * @var string $monster
         * @var string $drop
         */
        [$monster, $drop] = $this->context;

        return " ▶ $state - Fight $monster for $drop";
    }
}
