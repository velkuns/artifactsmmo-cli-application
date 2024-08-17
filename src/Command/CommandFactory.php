<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Command\Action\Action;

class CommandFactory
{
    /**
     * @template T of Action
     *
     * @param class-string<T> $commandClass
     * @param array<mixed> $arguments
     * @return T
     */
    public function new(
        string $commandClass,
        \Closure $closure,
        array $arguments,
    ): Action {
        return new $commandClass($closure, $arguments);
    }

    /**
     * @return CommandList
     */
    public function newList(): CommandList
    {
        return new CommandList();
    }
}
