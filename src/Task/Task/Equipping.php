<?php

declare(strict_types=1);

namespace Application\Task\Task;

use Application\Task;
use Application\Task\Action;
use Application\Entity\Character;
use Application\Enum\ItemType;
use Application\Enum\Slot;
use Application\Infrastructure\Client\ItemRepository;
use Application\VO\Item\Item;

class Equipping
{
    public function __construct(
        private readonly Task\ActionFactory $actionFactory,
        private readonly ItemRepository $itemRepository,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createTask(Character $character, string $code, bool $forceUnequip = false): Task\Task
    {
        $task = $this->actionFactory->newTask();

        $item = $this->itemRepository->findItem(Item::class, $code);
        if ($item === null) {
            return $task;
        }

        $slots = ItemType::from($item->type)->slot();

        if (!\is_array($slots)) {
            return $this->handleUniqueSlot($character, $item, $slots, $forceUnequip);
        }

        return $this->handleMultipleSlots($character, $item, $slots, $forceUnequip);
    }

    private function handleUniqueSlot(
        Character $character,
        Item $item,
        Slot $slot,
        bool $forceUnequip,
    ): Task\Task {
        echo "handle unique slot !\n";
        $task = $this->actionFactory->newTask();

        $hasEquipment = $character->hasEquipment($slot);
        if ($forceUnequip && $hasEquipment) {
            $task->enqueue($this->actionFactory->new(Action\Unequip::class, $character->unequip(...), [$slot]));
            $hasEquipment = false;
        }

        if (!$hasEquipment) {
            $task->enqueue($this->actionFactory->new(Action\Equip::class, $character->equip(...), [$slot, $item->code]));
        }

        return $task;
    }

    /**
     * @param Slot[] $slots
     */
    private function handleMultipleSlots(
        Character $character,
        Item $item,
        array $slots,
        bool $forceUnequip,
    ): Task\Task {
        echo "handle multiple slots !\n";
        $task = $this->actionFactory->newTask();

        $freeSlot = null;
        foreach ($slots as $slot) {
            if (!$character->hasEquipment($slot)) {
                $freeSlot = $slot;
                break;
            }
        }

        if ($forceUnequip && $freeSlot === null) {
            $freeSlot = \reset($slots);
            $task->enqueue($this->actionFactory->new(Action\Unequip::class, $character->unequip(...), [$freeSlot]));
        }

        if ($freeSlot !== null) {
            $task->enqueue($this->actionFactory->new(Action\Equip::class, $character->equip(...), [$freeSlot, $item->code]));
        }

        return $task;
    }
}
