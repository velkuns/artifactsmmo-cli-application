<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\Character;
use Application\Infrastructure\Client\ItemRepository;
use Application\VO\Item\Item;

class UpgradeEquipmentService
{
    public function __construct(
        private readonly ItemRepository $itemRepository,
    ) {}

    /**
     * @return list<Item>
     * @throws \Throwable
     */
    public function needUpgradeWeapon(Character $character): array
    {
        $items = $this->itemRepository->findAllCraftableItem(
            'weaponcrafting',
            $character->skills->weaponCrafting->level,
        );

        return $items;
    }
}
