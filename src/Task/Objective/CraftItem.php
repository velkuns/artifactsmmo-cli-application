<?php

declare(strict_types=1);

namespace Application\Task\Objective;

use Application\Task;
use Application\Task\Task\Crafting;
use Application\Task\Task\Equipping;
use Application\Task\Task\Gathering;
use Application\Entity\Character;
use Application\Infrastructure\Client\ItemRepository;
use Application\Infrastructure\Client\ResourceRepository;
use Application\Service\Helper\MapTrait;
use Application\VO\Item\Item;
use Velkuns\ArtifactsMMO\VO\SimpleItem;

class CraftItem
{
    use MapTrait;

    public function __construct(
        private readonly ItemRepository $itemRepository,
        private readonly ResourceRepository $resourceRepository,
        private readonly Gathering $gathering,
        private readonly Crafting $crafting,
        private readonly Equipping $equipping,
        private readonly Task\ActionFactory $actionFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createObjective(Character $character, string $code, int $quantity, bool $doEquip = false): Task\Objective
    {
        $objective = new Task\Objective();

        $item = $this->itemRepository->findItem(Item::class, $code);

        if ($item === null || $item->craft === null || !$item->craftableBy($character)) {
            return $objective;
        }

        $realQuantity = (int) ceil($quantity / $item->craft->quantity);

        /** @var \SplStack<array{0: SimpleItem, 1: int}> $stack */
        $stack = new \SplStack();
        $stack = $this->addResources($stack, $item->craft->items, $realQuantity);

        while (!$stack->isEmpty()) {
            [$craftItem, $realQuantity] = $stack->pop();
            echo "Process {$craftItem->code} [x$realQuantity]\n";

            $resource         = $this->itemRepository->findItem(Item::class, $craftItem->code);
            $resourceQuantity = $craftItem->quantity * $realQuantity;

            if ($resource === null) {
                continue;
            }

            if ($resource->craft === null) {
                if ($resource->type === 'resource' && $resource->subType === 'mining') {
                    $resource = $this->resourceRepository->findBestByDrop($resource->code, $character);
                    $objective->unshift($this->gathering->createTask($character, $resource->code, $resourceQuantity));
                }
                continue;
            }

            if (!$resource->craftableBy($character)) {
                continue; // TODO: handle farm
            }

            $stack = $this->addResources($stack, $resource->craft->items, $resourceQuantity);
            $objective->unshift($this->crafting->createTask($character, $craftItem->code, $resourceQuantity));
        }

        //~ Finally craft the item
        $objective->enqueue($this->crafting->createTask($character, $code, $quantity));

        //~ And equip if necessary
        if ($doEquip) {
            $objective->enqueue($this->equipping->createTask($character, $code, true));
        }

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
}
