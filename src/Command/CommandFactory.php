<?php

declare(strict_types=1);

namespace Application\Command;

class CommandFactory
{
    /**
     * @template T of Command
     *
     * @param class-string<T> $commandClass
     * @param array<mixed> $arguments
     * @return T
     */
    public function new(
        string $commandClass,
        \Closure $closure,
        array $arguments,
    ): Command {
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
