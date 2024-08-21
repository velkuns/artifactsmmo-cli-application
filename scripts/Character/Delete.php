<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character;

use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\CharactersClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\Body\BodyDeleteCharacter;

/**
 * @codeCoverageIgnore
 */
class Delete extends AbstractScript
{
    private const ENABLED = false; // Currently disabled to avoid any mistake :)

    public function __construct(
        private readonly CharactersClient $charactersClient,
    ) {
        $this->setDescription('Delete a character');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: ''))
                ->add(new Option(shortName: null, longName: 'simulate', description: 'Do a simulation of actions', mandatory: false, hasArgument: false, default: false)),
        );
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     * @throws \Throwable
     */
    public function run(): void
    {
        $name = (string) $this->options()->value('n', 'name');

        if (self::ENABLED) {
            $this->charactersClient->deleteCharacter(new BodyDeleteCharacter($name));
        }
    }
}
