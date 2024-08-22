<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Service\Helper\ItemEffectTrait;
use Application\Service\Helper\ItemTrait;
use Application\VO\Item;
use Velkuns\ArtifactsMMO\Client\GeClient;

class GrandExchangeRepository
{
    use ItemTrait;
    use ItemEffectTrait;

    public function __construct(private readonly GeClient $client) {}

    /**
     * @throws \Throwable
     */
    public function find(string $code): Item\GeItem
    {
        $data = $this->client->getGeItem($code);
        return new Item\GeItem($data->code, $data->stock, $data->sellPrice ?? 0, $data->buyPrice ?? 0);
    }

    /**
     * @return Item\GeItem[]
     * @throws \Throwable
     */
    public function findAll(): array
    {
        $collection = $this->client->getAllGeItems();
        $items      = [];
        foreach ($collection as $data) {
            $items[] = new Item\GeItem($data->code, $data->stock, $data->sellPrice ?? 0, $data->buyPrice ?? 0);
        }
        return $items;
    }
}
