<?php

declare(strict_types=1);

namespace Application\Command\Action;

abstract class Action
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

    public function simulate(): bool
    {
        $this->render();

        return true;
    }

    private function render(): void
    {
        $actionName = \basename(\str_replace('\\', '/', $this::class));
        $args       = !empty($this->arguments) ? '(with args: ' . json_encode($this->arguments) . ')' : '';
        echo "> $actionName $args\n";
    }
}
