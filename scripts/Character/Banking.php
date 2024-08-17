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
use Application\Command\CommandList;
use Application\Entity\Character;
use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Application\Service\Banking as BankingService;
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
class Banking extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly CommandHandler $commandHandler,
        private readonly BankingService $banking,
    ) {
        $this->setDescription('Gathering task');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(longName: 'deposit', description: 'To deposit something to bank', default: false))
                ->add(new Option(longName: 'withdraw', description: 'To withdraw something to bank', default: false))
                ->add(new Option(longName: 'gold', description: 'To deposit or withdraw gold', default: false))
                ->add(new Option(longName: 'all', description: 'Do action on all object', default: false))
                ->add(new Option(shortName: 'i', longName: 'item', description: 'Item code to deposit or withdraw', hasArgument: true, default: ''))
                ->add(new Option(shortName: 'q', longName: 'quantity', description: 'Quantity of monster to fight', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: 's', longName: 'simulate', description: 'Do a simulation of actions', default: false)),
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
        $isDeposit  = (bool) $this->options()->value('deposit');
        $isWithdraw = (bool) $this->options()->value('withdraw');
        $isGold     = (bool) $this->options()->value('gold');
        $isAll      = (bool) $this->options()->value('all');

        $quantity = (int) $this->options()->value('q', 'quantity');

        if ($isDeposit && $isWithdraw || !$isDeposit && !$isWithdraw || $isAll && $isWithdraw && !$isGold) {
            throw new \UnexpectedValueException('Cannot doing deposit & withdraw at same time or doing nothing !');
        }

        $character = $this->getCharacter($this->options(), $this->characterRepository);

        if ($isGold) {
            $commands = $this->handleGolds($character, $isWithdraw, $isAll, $quantity);
        } else {
            $commands = $this->handleItems($character, $isWithdraw, $isAll, $quantity);
        }

        $this->commandHandler->handleList($character, $commands, $this->isSimulation($this->options()));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    private function handleGolds(Character $character, bool $isWithdraw, bool $isAll, int $quantity): CommandList
    {
        if ($isWithdraw) {
            return $this->banking->createWithdrawGoldCommands($character, $quantity, $isAll);
        }

        return $this->banking->createDepositGoldCommands($character, $quantity, $isAll);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    private function handleItems(Character $character, bool $isWithdraw, bool $isAll, int $quantity): CommandList
    {
        $item  = (string) $this->options()->value('i', 'item');

        if ($isWithdraw) {
            return $this->banking->createWithdrawItemCommands($character, $item, $quantity);
        }

        if ($isAll && empty($item)) {
            return $this->banking->createDepositAllItemsCommands($character);
        }

        //~ TODO: handle all quantity on inventory item

        return $this->banking->createDepositItemCommands($character, $item, $quantity);
    }
}
