<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script;

use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;

/**
 * Run command with:
 * <code>
 * Use    : bin/console example [OPTION]...
 * OPTIONS:
 * -o ARG, --option=ARG                  Option description
 * </code>
 *
 * @codeCoverageIgnore
 */
class Example extends AbstractScript
{
    public function __construct()
    {
        $this->setDescription('Example script');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(
                    new Option(
                        shortName: 'o',
                        longName: 'option',
                        description: 'Option description',
                        mandatory: false,
                        hasArgument: true,
                        default: null,
                    ),
                ),
        );
    }

    public function help(): void
    {
        (new Help(
            substr(self::class, (int) strrpos(self::class, '\\') + 1),
            $this->declaredOptions(),
            $this->output(),
            $this->options(),
        ))->display();
    }

    public function run(): void
    {
        $this->output()->writeln('This is an example script');
        $this->output()->writeln('Option "o / option" has argument value:' . $this->options()->value('o', 'option'));
    }
}
