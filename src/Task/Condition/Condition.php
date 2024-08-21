<?php

declare(strict_types=1);

namespace Application\Task\Condition;

use Application\Entity\Character;

interface Condition
{
    public function isValid(Character $character): bool;

    public function render(Character $character): string;
}
