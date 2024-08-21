<?php

declare(strict_types=1);

namespace Application\Task\Action;

class Gather extends Action
{
    protected function renderAction(string $state = '🆕'): string
    {
        /** @var string $resource */
        [$resource] = $this->context;
        return " ▶ $state - Gather $resource";
    }
}
