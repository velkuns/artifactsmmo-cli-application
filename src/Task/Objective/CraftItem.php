<?php

declare(strict_types=1);

namespace Application\Task\Objective;

use Application\Infrastructure\Client\BankRepository;
use Application\Infrastructure\Client\MonsterRepository;
use Application\Service\Helper\BankTrait;
use Application\Service\Helper\InventoryTrait;
use Application\Service\Renderer\ObjectiveRenderer;
use Application\Task;
use Application\Entity\Character;
use Application\Infrastructure\Client\ItemRepository;
use Application\Task\Task\Fighting;
use Application\VO\Item\Item;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\SimpleItem;

class CraftItem
{
    use BankTrait;
    use InventoryTrait;

    public function __construct(
        private readonly ObjectiveRenderer $renderer,
        private readonly ItemRepository $itemRepository,
        private readonly MonsterRepository $monsterRepository,
        private readonly BankRepository $bankRepository,
        private readonly Task\Task\Gathering $gathering,
        private readonly Task\Task\Crafting $crafting,
        private readonly Task\Task\Banking $banking,
        private readonly Task\Task\Equipping $equipping,
        private readonly Task\Task\Exchanging $exchanging,
        private readonly Fighting $fighting,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createObjective(
        Character $character,
        string $code,
        int $quantity,
        bool $doEquip = false,
        bool $doSell = false,
        bool $doStore = false,
    ): Task\Objective {
        $this->renderer->displayTitle('Objective: CraftItem');
        $this->renderer->displaySubTitle('Preparing Objective');
        $this->renderer->stateInProgress('Computing task...');

        $objective = new Task\Objective();

        $item = $this->itemRepository->findItem(Item::class, $code);

        if ($item === null || $item->craft === null || !$item->craftableBy($character)) {
            return $objective;
        }

        $realQuantity = (int) ceil($quantity / $item->craft->quantity);

        /** @var \SplStack<array{0: SimpleItem, 1: int}> $stack */
        $stack = new \SplStack();
        $stack = $this->addResources($stack, $item->craft->items, $realQuantity);

        $withdrawItems = [];

        while (!$stack->isEmpty()) {
            [$craftItem, $realQuantity] = $stack->pop();

            $resource         = $this->itemRepository->findItem(Item::class, $craftItem->code);
            $resourceQuantity = $craftItem->quantity * $realQuantity;

            if ($resource === null) {
                continue;
            }

            //~ Handle inventory & bank check
            $requestedQuantity = $this->inventoryCheck($character, $resource, $resourceQuantity);
            $requestedQuantity = $this->bankCheck($resource, $requestedQuantity, $withdrawItems);

            //~ Do get anymore resource if we have all in bank;
            if ($requestedQuantity === 0) {
                continue;
            }

            if ($resource->craft === null) {
                $this->handleCraft($character, $resource, $resourceQuantity, $objective);
                $this->handleFight($character, $resource, $resourceQuantity, $objective);
                continue;
            }

            if (!$resource->craftableBy($character)) {
                continue; // TODO: handle farm
            }

            $stack = $this->addResources($stack, $resource->craft->items, $resourceQuantity);
            $objective->unshift($this->crafting->createTask($character, $craftItem->code, $resourceQuantity));
        }

        //~ Before starting, get needed resources already in bank
        if ($withdrawItems !== []) {
            $objective->add(0, $this->banking->createWithdrawItemsTask($character, $withdrawItems));
        }

        //~ Finally craft the item
        $objective->enqueue($this->crafting->createTask($character, $code, $quantity));

        //~ And equip if necessary
        if ($doEquip) {
            $objective->enqueue($this->equipping->createTask($character, $code, true));
        }

        //~ Sell if necessary
        if ($doSell) {
            $objective->enqueue($this->exchanging->createSellTask($character, $code, $quantity));
        }

        //~ Sell if necessary
        if ($doStore) {
            $objective->enqueue($this->banking->createDepositItemTask($character, $code, $quantity));
        }

        $this->renderer->stateDone();

        return $objective;
    }

    /**
     * @param \SplStack<array{0: SimpleItem, 1: int}> $stack
     * @param SimpleItem[] $resources
     * @return \SplStack<array{0: SimpleItem, 1: int}>
     */
    private function addResources(\SplStack $stack, array $resources, int $realQuantity): \SplStack
    {
        foreach ($resources as $resource) {
            $stack->push([$resource, $realQuantity]);
        }

        return $stack;
    }

    private function inventoryCheck(Character $character, Item $item, int $itemQuantity): int
    {
        $nbItemInInventory = $this->countItemInInventory($character, $item->code);
        if ($nbItemInInventory > 0) {
            $quantityFromInventory = min($nbItemInInventory, $itemQuantity);
            $itemQuantity -= $quantityFromInventory;
        }

        return $itemQuantity;
    }

    /**
     * @param array<array{code: string, quantity: int}> $withdrawItems
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    private function bankCheck(Item $item, int $itemQuantity, array &$withdrawItems): int
    {
        $nbItemInBank = $this->countItemInBank($this->bankRepository, $item->code);
        if ($nbItemInBank > 0 && $itemQuantity > 0) {
            $quantityFromBank = min($nbItemInBank, $itemQuantity);
            $withdrawItems[]  = ['code' => $item->code, 'quantity' => $quantityFromBank];
            $itemQuantity -= $quantityFromBank;
        }

        return $itemQuantity;
    }

    /**
     * @throws \Throwable
     */
    private function handleCraft(Character $character, Item $item, int $quantity, Task\Objective $objective): void
    {
        if ($item->type === 'resource' && \in_array($item->subType, ['mining', 'woodcutting', ''])) {
            $objective->unshift($this->gathering->createTaskForDrop($character, $item->code, $quantity));
        }
    }

    /**
     * @throws \Throwable
     */
    private function handleFight(Character $character, Item $item, int $quantity, Task\Objective $objective): void
    {
        if ($item->type === 'resource' && $item->subType === 'mob') {
            $resource = $this->monsterRepository->findBestByDrop($item->code, $character);
            $objective->unshift($this->fighting->createTask($character, $resource->code, $quantity));
        }
    }
}
