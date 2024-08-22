<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Service\Helper\ItemEffectTrait;
use Application\Service\Helper\ItemTrait;
use Application\VO\Item;
use Velkuns\ArtifactsMMO\Client\ItemsClient;

class ItemRepository
{
    use ItemTrait;
    use ItemEffectTrait;

    public function __construct(private readonly ItemsClient $client) {}

    /**
     * @return Item\Item[]
     * @throws \Throwable
     */
    public function findAllCraftableItem(
        string $craftSkill,
        int $maxLevel,
        int $minLevel = 0,
        string $craftMaterial = '',
    ): array {
        $items = [];

        if (empty($craftSkill)) {
            return $items;
        }

        $query = \array_filter([
            'craft_skill' => $craftSkill,
            'craft_material' => $craftMaterial,
            'min_level' => $minLevel,
            'max_level' => $maxLevel,
        ]);
        $data = $this->client->getAllItems($query);
        foreach ($data as $item) {
            $items[] = $this->newItem(Item\Item::class, $item);
        }

        return $items;
    }

    /**
     * @template T
     * @param class-string<T> $itemClass
     * @return T|null
     * @throws \Throwable
     */
    public function findItem(string $itemClass, string|null $code)
    {
        if (empty($code)) {
            return null;
        }

        $data = $this->client->getItem($code);
        return $this->newItem($itemClass, $data->item);
    }

    /**
     * @throws \Throwable
     */
    public function findWeapon(string|null $code): Item\Weapon|null
    {
        return $this->findItem(Item\Weapon::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findShield(string|null $code): Item\Shield|null
    {
        return $this->findItem(Item\Shield::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findHelmet(string|null $code): Item\Helmet|null
    {
        return $this->findItem(Item\Helmet::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findBodyArmor(string|null $code): Item\BodyArmor|null
    {
        return $this->findItem(Item\BodyArmor::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findLegArmor(string|null $code): Item\LegArmor|null
    {
        return $this->findItem(Item\LegArmor::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findBoots(string|null $code): Item\Boots|null
    {
        return $this->findItem(Item\Boots::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findArtifact(string|null $code): Item\Artifact|null
    {
        return $this->findItem(Item\Artifact::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findAmulet(string|null $code): Item\Amulet|null
    {
        return $this->findItem(Item\Amulet::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findRing(string|null $code): Item\Ring|null
    {
        return $this->findItem(Item\Ring::class, $code);
    }

    /**
     * @throws \Throwable
     */
    public function findConsumable(string|null $code, int $quantity = 0): Item\Consumable|null
    {
        $consumable = $this->findItem(Item\Consumable::class, $code);

        if ($consumable !== null) {
            $consumable->quantity = $quantity;
        }

        return $consumable;
    }
}
