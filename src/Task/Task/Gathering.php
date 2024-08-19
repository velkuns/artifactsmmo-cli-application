<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\InventoryTrait;
use Application\Service\Helper\MapTrait;

class Gathering
{
    use MapTrait;
    use InventoryTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly Task\ActionFactory $actionFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createTask(Character $character, string $resource, int $quantity): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findResource($resource), $task);

        //~ Then enqueue main action
        $action = $this->actionFactory->gather($character, $this->hasEnoughItem(...), [$resource, $quantity]);
        $task->enqueue($action);

        return $task;
    }

}
