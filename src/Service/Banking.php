<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Command\Action;
use Application\Entity\Character;
use Application\Infrastructure\Client\BankRepository;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;

class Banking
{
    use MapTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly BankRepository $bankRepository,
        private readonly Command\CommandFactory $commandFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createDepositItemCommands(Character $character, string $code, int $quantity): Command\CommandList
    {
        return $this->createDepositItemsCommands($character, [['code' => $code, 'quantity' => $quantity]]);
    }

    /**
     * @throws \Throwable
     */
    public function createDepositAllItemsCommands(Character $character): Command\CommandList
    {
        $items = [];

        foreach ($character->inventory->inventory as $inventorySlot) {
            if (empty($inventorySlot->code) || $inventorySlot->quantity === 0) {
                continue;
            }

            $items[] = ['code' => $inventorySlot->code, 'quantity' => $inventorySlot->quantity];
        }

        return $this->createDepositItemsCommands($character, $items);
    }

    /**
     * @param list<array{code: string, quantity: int}> $items
     * @throws \Throwable
     */
    public function createDepositItemsCommands(Character $character, array $items): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findBank(), $commands);

        //~ Then enqueue main action
        foreach ($items as ['code' => $code, 'quantity' => $quantity]) {
            $command = $this->commandFactory->new(Action\DepositItems::class, $character->depositItem(...), [$code, $quantity]);
            $commands->enqueue($command);
        }

        return $commands;
    }

    /**
     * @throws \Throwable
     */
    public function createWithdrawItemCommands(Character $character, string $code, int $quantity): Command\CommandList
    {
        return $this->createWithdrawItemsCommands($character, [['code' => $code, 'quantity' => $quantity]]);
    }

    /**
     * @param list<array{code: string, quantity: int}> $items
     * @throws \Throwable
     */
    public function createWithdrawItemsCommands(Character $character, array $items): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findBank(), $commands);

        //~ Then enqueue main actions
        foreach ($items as ['code' => $code, 'quantity' => $quantity]) {
            $command = $this->commandFactory->new(Action\WithdrawItems::class, $character->withdrawItem(...), [$code, $quantity]);
            $commands->enqueue($command);
        }

        return $commands;
    }

    /**
     * @throws \Throwable
     */
    public function createDepositGoldCommands(Character $character, int $quantity, bool $isAll): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findBank(), $commands);

        //~ Calculate appropriate quantity
        $maxGolds = $character->gold;
        $quantity = $isAll ? $maxGolds : min($quantity, $maxGolds);

        //~ Then enqueue main action
        $command = $this->commandFactory->new(Action\DepositGolds::class, $character->depositGold(...), [$quantity]);
        $commands->enqueue($command);

        return $commands;
    }

    /**
     * @throws \Throwable
     */
    public function createWithdrawGoldCommands(Character $character, int $quantity, bool $isAll): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findBank(), $commands);

        //~ Calculate appropriate quantity
        $maxGolds = $this->bankRepository->getGolds();
        $quantity = $isAll ? $maxGolds : min($quantity, $maxGolds);

        //~ Then enqueue main action
        $command = $this->commandFactory->new(Action\WithdrawGolds::class, $character->withdrawGold(...), [$quantity]);
        $commands->enqueue($command);

        return $commands;
    }

}
