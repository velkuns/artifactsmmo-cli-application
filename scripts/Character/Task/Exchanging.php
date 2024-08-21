<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character\Task;

use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Application\Task\Task\Exchanging as ExchangingService;
use Application\Task\TaskHandler;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class Exchanging extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly TaskHandler $taskHandler,
        private readonly ExchangingService $exchanging,
    ) {
        $this->setDescription('Gathering task');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(shortName: 'i', longName: 'item', description: 'Item code to craft', mandatory: true, hasArgument: true, default: null))
                ->add(new Option(shortName: 'q', longName: 'quantity', description: 'Quantity of the resource to gather', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: 's', longName: 'sell', description: 'Sell an item', mandatory: false, hasArgument: false, default: false))
                ->add(new Option(shortName: 'b', longName: 'buy', description: 'Buy an item', mandatory: false, hasArgument: false, default: false))
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
        $code     = (string) $this->options()->value('i', 'item');
        $quantity = (int) $this->options()->value('q', 'quantity');
        $doSell   = (bool) $this->options()->value('s', 'sell');
        $doBuy    = (bool) $this->options()->value('b', 'buy');

        $character = $this->getCharacter($this->options(), $this->characterRepository);

        if ($doSell) {
            $task = $this->exchanging->createSellTask($character, $code, $quantity);
        }

        if ($doBuy) {
            $task = $this->exchanging->createBuyTask($character, $code, $quantity);
        }

        if (!isset($task)) {
            throw new \UnexpectedValueException('sell or buy option required');
        }

        $this->taskHandler->handle($character, $task, $this->isSimulation($this->options()));
    }
}
