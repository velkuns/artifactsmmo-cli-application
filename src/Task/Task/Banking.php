<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Task\Action;
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
        private readonly Task\ActionFactory $actionFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createDepositItemTask(Character $character, string $code, int $quantity): Task\Task
    {
        return $this->createDepositItemsTask($character, [['code' => $code, 'quantity' => $quantity]]);
    }

    /**
     * @throws \Throwable
     */
    public function createDepositAllItemsTask(Character $character): Task\Task
    {
        $items = [];

        foreach ($character->inventory->inventory as $inventorySlot) {
            if (empty($inventorySlot->code) || $inventorySlot->quantity === 0) {
                continue;
            }

            $items[] = ['code' => $inventorySlot->code, 'quantity' => $inventorySlot->quantity];
        }

        return $this->createDepositItemsTask($character, $items);
    }

    /**
     * @param list<array{code: string, quantity: int}> $items
     * @throws \Throwable
     */
    public function createDepositItemsTask(Character $character, array $items): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findBank(), $task);

        //~ Then enqueue main action
        foreach ($items as ['code' => $code, 'quantity' => $quantity]) {
            $action = $this->actionFactory->new(Action\DepositItems::class, $character->depositItem(...), [$code, $quantity]);
            $task->enqueue($action);
        }

        return $task;
    }

    /**
     * @throws \Throwable
     */
    public function createWithdrawItemTask(Character $character, string $code, int $quantity): Task\Task
    {
        return $this->createWithdrawItemsTask($character, [['code' => $code, 'quantity' => $quantity]]);
    }

    /**
     * @param list<array{code: string, quantity: int}> $items
     * @throws \Throwable
     */
    public function createWithdrawItemsTask(Character $character, array $items): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findBank(), $task);

        //~ Then enqueue main actions
        foreach ($items as ['code' => $code, 'quantity' => $quantity]) {
            $action = $this->actionFactory->new(Action\WithdrawItems::class, $character->withdrawItem(...), [$code, $quantity]);
            $task->enqueue($action);
        }

        return $task;
    }

    /**
     * @throws \Throwable
     */
    public function createDepositGoldTask(Character $character, int $quantity, bool $isAll): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findBank(), $task);

        //~ Calculate appropriate quantity
        $maxGolds = $character->gold;
        $quantity = $isAll ? $maxGolds : min($quantity, $maxGolds);

        //~ Then enqueue main action
        $action = $this->actionFactory->new(Action\DepositGolds::class, $character->depositGold(...), [$quantity]);
        $task->enqueue($action);

        return $task;
    }

    /**
     * @throws \Throwable
     */
    public function createWithdrawGoldTask(Character $character, int $quantity, bool $isAll): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findBank(), $task);

        //~ Calculate appropriate quantity
        $maxGolds = $this->bankRepository->getGolds();
        $quantity = $isAll ? $maxGolds : min($quantity, $maxGolds);

        //~ Then enqueue main action
        $action = $this->actionFactory->new(Action\WithdrawGolds::class, $character->withdrawGold(...), [$quantity]);
        $task->enqueue($action);

        return $task;
    }

}
