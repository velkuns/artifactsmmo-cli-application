<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Entity\Character;
use Psr\Clock\ClockInterface;

class CommandFactory
{
    public function __construct(
        private readonly ClockInterface $clock
    ) {}

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
        Character $character,
    ): Command {
        return new $commandClass($this->clock, $closure, $arguments, $character);
    }
}
