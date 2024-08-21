<?php

declare(strict_types=1);

namespace Application\Service\Renderer;

use Eureka\Component\Console\Terminal\Terminal;

class ObjectiveRenderer
{
    use DisplayTrait;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }
}
