<?php

declare(strict_types=1);

namespace Application\Command;

abstract class Command
{
    abstract public function isExecutable(): bool;

    public function execute(): mixed
    {
        if (!$this->isExecutable()) {
            return false;
        }

        return \call_user_func($this->callable, $this->arguments);
    }
}
