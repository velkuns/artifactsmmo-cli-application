<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Task\Action;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;
use Application\Task\Condition\InventoryItemMissingCondition;

class Fighting
{
    use MapTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly Task\ActionFactory $actionFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createTask(Character $character, string $monster, string $drop, int $quantity): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findMonster($monster), $task);

        //~ Then enqueue main action
        $action = $this->actionFactory->fight(
            $character,
            $monster,
            $drop,
            new InventoryItemMissingCondition($drop, $quantity),
        );
        $task->enqueue($action);

        return $task;
    }

}
