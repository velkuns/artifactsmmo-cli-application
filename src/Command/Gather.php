<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Entity\Character;
use Application\VO\Position;
use Psr\Clock\ClockInterface;

class Gather extends Command
{
    /**
     * @param array{0: Position} $arguments
     */
    public function __construct(
        protected readonly ClockInterface $clock,
        protected readonly \Closure $callable,
        protected readonly array $arguments,
        protected readonly Character $character,
    ) {}

    public function isExecutable(): bool
    {
        $cooldownEnd = $this->character->cooldown->date;

        if ($cooldownEnd === null) {
            return true;
        }

        $now = $this->clock->now();

        return $cooldownEnd < $now;
    }
}
