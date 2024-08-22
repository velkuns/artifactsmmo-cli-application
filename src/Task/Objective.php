<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Entity\Character;
use Application\Task\Condition\Condition;

/**
 * @extends \SplQueue<Task>
 */
class Objective extends \SplQueue
{
    private Condition|null $repeatableCondition = null;

    public function setRepeatableCondition(Condition|null $condition): self
    {
        $this->repeatableCondition = $condition;

        return $this;
    }

    public function repeatable(Character $character): bool
    {
        if ($this->repeatableCondition === null) {
            return false;
        }

        return $this->repeatableCondition->isValid($character);
    }

    public function new(): Objective
    {
        return (new Objective())->setRepeatableCondition($this->repeatableCondition);
    }
}
