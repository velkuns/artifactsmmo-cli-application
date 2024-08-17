<?php

declare(strict_types=1);

namespace Application\Command;

abstract class Command
{
    /**
     * @param array<mixed> $arguments
     */
    public function __construct(
        protected readonly \Closure $callable,
        protected readonly array $arguments,
    ) {}

    public function execute(): mixed
    {
        $this->render();

        return \call_user_func_array($this->callable, $this->arguments);
    }

    private function render(): void
    {
        $actionName = \basename(\str_replace('\\', '/', $this::class));
        $args       = !empty($this->arguments) ? '(with args: ' . json_encode($this->arguments) . ')' : '';
        echo "> $actionName $args\n";
    }
}
