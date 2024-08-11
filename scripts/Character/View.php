<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character;

use Application\Entity\Character;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\CharactersClient;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class View extends AbstractScript
{
    public function __construct(
        private readonly CharactersClient $charactersClient,
        private readonly MyClient $myClient,
    ) {
        $this->setDescription('Example script');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(
                    new Option(
                        shortName: 'n',
                        longName: 'name',
                        description: 'Character name',
                        mandatory: true,
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

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function run(): void
    {
        $name      = $this->options()->value('n', 'name');

        if (empty($name)) {
            throw new \UnexpectedValueException('Name is missing!');
        }

        $character = new Character($name, $this->charactersClient, $this->myClient);

        var_export($character->info());
    }
}
