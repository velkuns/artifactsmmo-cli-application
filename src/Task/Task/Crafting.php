<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Entity\Character;
use Application\Enum\SkillType;
use Application\Infrastructure\Client\ItemRepository;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;
use Application\VO\Item\Item;

class Crafting
{
    use MapTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly ItemRepository $itemRepository,
        private readonly Task\ActionFactory $actionFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createTask(Character $character, string $code, int $quantity): Task\Task
    {
        $task = $this->actionFactory->newTask();

        $item = $this->itemRepository->findItem(Item::class, $code);

        if ($item === null || $item->craft === null) {
            return $task;
        }

        try {
            $skillType = SkillType::from($item->craft->skill);
        } catch (\TypeError) {
            return $task;
        }

        //~ Handle move if necessary
        $task = $this->handleMove($character, $this->mapRepository->findWorkshop($skillType->value), $task);

        //~ Then enqueue main action
        $action = $this->actionFactory->craft($character, $code, $quantity);
        $task->enqueue($action);

        return $task;
    }
}
