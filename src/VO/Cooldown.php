<?php

declare(strict_types=1);

namespace Application\VO;

class Cooldown
{
    public function __construct(
        public int $cooldown,
        public \DateTimeImmutable|null $date = null,
    ) {}
}
