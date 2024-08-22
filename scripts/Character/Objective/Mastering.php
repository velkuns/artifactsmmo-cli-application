<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character\Objective;

use Application\Task\Objective;
use Application\Task\ObjectiveHandler;
use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class Mastering extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly ObjectiveHandler $objectiveHandler,
        private readonly Objective\Mastering $mastering,
    ) {
        $this->setDescription('Level up the character in each discipline as objective');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(shortName: 'l', longName: 'level', description: 'Level objective to reach', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: '', longName: 'simulate', description: 'Do a simulation of actions', mandatory: false, hasArgument: false, default: false)),
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
        $character = $this->getCharacter($this->options(), $this->characterRepository);
        $simulate  = $this->isSimulation($this->options());
        $level     = (int) $this->options()->value('l', 'level');

        $objectives = $this->mastering->createObjectives($character, $level);
        $this->objectiveHandler->handleMultiple($character, $objectives, $simulate);
    }
}
