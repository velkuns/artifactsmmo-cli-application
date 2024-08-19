<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Task\Action;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;

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
    public function createTask(Character $character, string $code, int $quantity): Task\Task
    {
        $task = $this->actionFactory->newTask();

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findMonster($code), $task);

        //~ Then enqueue main action
        for ($i = 0; $i < $quantity; $i++) {
            $action = $this->actionFactory->new(Action\Fight::class, $character->fight(...), []);
            $task->enqueue($action);
        }

        return $task;
    }

}
