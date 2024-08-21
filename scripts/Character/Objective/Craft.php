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
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class Craft extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly ObjectiveHandler $objectiveHandler,
        private readonly Objective\CraftItem $craftItem,
    ) {
        $this->setDescription('Craft item as objective');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(shortName: 'i', longName: 'item', description: 'Item code to craft', mandatory: true, hasArgument: true, default: null))
                ->add(new Option(shortName: 'q', longName: 'quantity', description: 'Quantity of the item to craft', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: 'e', longName: 'equip', description: 'Equip the element crafted', mandatory: false, hasArgument: false, default: false))
                ->add(new Option(shortName: 's', longName: 'sell', description: 'Sell the element crafted on G.E', mandatory: false, hasArgument: false, default: false))
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

        $code     = (string) $this->options()->value('i', 'item');
        $quantity = (int) $this->options()->value('q', 'quantity');
        $doEquip  = (bool) $this->options()->value('e', 'equip');
        $doSell   = (bool) $this->options()->value('s', 'sell');

        $objective = $this->craftItem->createObjective($character, $code, $quantity, $doEquip, $doSell);
        $this->objectiveHandler->handle($character, $objective, $simulate);
    }
}
