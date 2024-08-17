<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Command\Action;
use Application\Entity\Character;
use Application\Enum\ItemType;
use Application\Enum\Slot;
use Application\Infrastructure\Client\ItemRepository;
use Application\VO\Item\Item;

class Equipping
{
    public function __construct(
        private readonly Command\CommandFactory $commandFactory,
        private readonly ItemRepository $itemRepository,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createCommands(Character $character, string $code, bool $forceUnequip = false): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        $item = $this->itemRepository->findItem(Item::class, $code);
        if ($item === null) {
            return $commands;
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
    ): Command\CommandList {
        echo "handle unique slot !\n";
        $commands = $this->commandFactory->newList();

        $hasEquipment = $character->hasEquipment($slot);
        if ($forceUnequip && $hasEquipment) {
            $commands->enqueue($this->commandFactory->new(Action\Unequip::class, $character->unequip(...), [$slot]));
            $hasEquipment = false;
        }

        if (!$hasEquipment) {
            $commands->enqueue($this->commandFactory->new(Action\Equip::class, $character->equip(...), [$slot, $item->code]));
        }

        return $commands;
    }

    /**
     * @param Slot[] $slots
     */
    private function handleMultipleSlots(
        Character $character,
        Item $item,
        array $slots,
        bool $forceUnequip,
    ): Command\CommandList {
        echo "handle multiple slots !\n";
        $commands = $this->commandFactory->newList();

        $freeSlot = null;
        foreach ($slots as $slot) {
            if (!$character->hasEquipment($slot)) {
                $freeSlot = $slot;
                break;
            }
        }

        if ($forceUnequip && $freeSlot === null) {
            $freeSlot = \reset($slots);
            $commands->enqueue($this->commandFactory->new(Action\Unequip::class, $character->unequip(...), [$freeSlot]));
        }

        if ($freeSlot !== null) {
            $commands->enqueue($this->commandFactory->new(Action\Equip::class, $character->equip(...), [$freeSlot, $item->code]));
        }

        return $commands;
    }
}
