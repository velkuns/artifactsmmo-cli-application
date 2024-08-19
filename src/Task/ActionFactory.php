<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Task\Action\Action;
use Application\Task\Action\Craft;
use Application\Task\Action\Gather;
use Application\Entity\Character;

class ActionFactory
{
    /**
     * @template T of Action
     *
     * @param class-string<T> $actionClass
     * @param array<mixed> $arguments
     * @param array<mixed> $argumentsForRepeatableCondition
     * @return T
     */
    public function new(
        string $actionClass,
        \Closure $closure,
        array $arguments,
        \Closure|null $repeatableCondition = null,
        array $argumentsForRepeatableCondition = [],
    ): Action {
        return new $actionClass($closure, $arguments, $repeatableCondition, $argumentsForRepeatableCondition);
    }

    /**
     * @param array<mixed> $argumentsForRepeatableCondition
     */
    public function gather(
        Character $character,
        \Closure|null $repeatableCondition = null,
        array $argumentsForRepeatableCondition = [],
    ): Gather {
        return new Gather($character->gather(...), [], $repeatableCondition, $argumentsForRepeatableCondition);
    }

    public function craft(Character $character, string $code, int $quantity): Craft
    {
        return new Craft($character->craft(...), [$code, $quantity]);
    }

    /**
     * @return Task
     */
    public function newTask(): Task
    {
        return new Task();
    }
}
