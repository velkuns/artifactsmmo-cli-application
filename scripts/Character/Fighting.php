<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character;

use Application\Command\CommandHandler;
use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Application\Service\Fighting as FightingService;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class Fighting extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly CommandHandler $commandHandler,
        private readonly FightingService $fighting,
    ) {
        $this->setDescription('Gathering task');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(shortName: 'r', longName: 'monster', description: 'Monster to fight', mandatory: true, hasArgument: true, default: null))
                ->add(new Option(shortName: 'q', longName: 'quantity', description: 'Quantity of monster to fight', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: 's', longName: 'simulate', description: 'Do a simulation of actions', mandatory: false, hasArgument: false, default: false)),
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
     * @throws \Throwable
     */
    public function run(): void
    {
        $monster  = (string) $this->options()->value('m', 'monster');
        $quantity = (int) $this->options()->value('q', 'quantity');

        $character = $this->getCharacter($this->options(), $this->characterRepository);
        $commands  = $this->fighting->createCommands($character, $monster, $quantity);

        $this->commandHandler->handleList($character, $commands, $this->isSimulation($this->options()));
    }
}
