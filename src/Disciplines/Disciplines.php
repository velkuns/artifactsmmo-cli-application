<?php

declare(strict_types=1);

namespace Application\Disciplines;

class Disciplines
{
    public function __construct(public readonly Discipline $main, public readonly Discipline|null $secondary) {}
}
