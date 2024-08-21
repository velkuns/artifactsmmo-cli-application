<?php

declare(strict_types=1);

namespace Application\Service;

use Eureka\Component\Console\Output\StreamOutput;

class OutputFactory
{
    public static function stdout(): StreamOutput
    {
        return new StreamOutput(\STDOUT, false);
    }
}
