<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character;

use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Application\Service\Renderer\CharacterRenderer;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\CharactersClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\Body\BodyAddCharacter;

/**
 * @codeCoverageIgnore
 */
class Create extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly CharactersClient $charactersClient,
    ) {
        $this->setDescription('Create new character');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: ''))
                ->add(new Option(shortName: 's', longName: 'skin', description: 'Character skin (men1, men2, men3, woman1, woman2, woman3', mandatory: true, hasArgument: true, default: ''))
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
        $skin = (string) $this->options()->value('s', 'skin');

        $this->charactersClient->createCharacter(new BodyAddCharacter($name, $skin));

        $character = $this->getCharacter($this->options(), $this->characterRepository);
        echo (new CharacterRenderer())->render($character);
    }
}
