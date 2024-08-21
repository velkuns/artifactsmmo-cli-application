<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Infrastructure\Client\GrandExchangeRepository;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;
use Application\Task;
use Application\Entity\Character;

class Exchanging
{
    use MapTrait;

    public function __construct(
        private readonly Task\ActionFactory $actionFactory,
        private readonly MapRepository $mapRepository,
        private readonly GrandExchangeRepository $grandExchangeRepository,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createSellTask(Character $character, string $code, int $quantity = 1): Task\Task
    {
        $task = $this->actionFactory->newTask();
        $item = $this->grandExchangeRepository->find($code);

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findGrandExchange(), $task);

        $task->enqueue($this->actionFactory->sell($character, $item->code, $quantity, $item));

        return $task;
    }

    /**
     * @throws \Throwable
     */
    public function createBuyTask(Character $character, string $code, int $quantity = 1): Task\Task
    {
        $task = $this->actionFactory->newTask();
        $item = $this->grandExchangeRepository->find($code);

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findGrandExchange(), $task);

        $task->enqueue($this->actionFactory->buy($character, $item->code, $quantity, $item));

        return $task;
    }
}
