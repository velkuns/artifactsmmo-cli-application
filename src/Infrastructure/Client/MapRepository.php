<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Velkuns\ArtifactsMMO\Client\MapsClient;
use Velkuns\ArtifactsMMO\VO\Map;

class MapRepository
{
    public function __construct(private readonly MapsClient $client) {}

    /**
     * @return list<Map>
     * @throws \Throwable
     */
    public function findAllResources(): array
    {
        return $this->client->getAllMaps(['content_type' => 'resource']);
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findResource(string $code): array
    {
        return $this->client->getAllMaps(['content_type' => 'resource', 'content_code' => $code]);
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findMonster(string $code): array
    {
        return $this->client->getAllMaps(['content_type' => 'monster', 'content_code' => $code]);
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findBank(): array
    {
        return $this->client->getAllMaps(['content_type' => 'bank', 'content_code' => 'bank']);
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findWorkshop(string $code): array
    {
        return $this->client->getAllMaps(['content_type' => 'workshop', 'content_code' => $code]);
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findGrandExchange(): array
    {
        return $this->client->getAllMaps(['content_type' => 'grand_exchange', 'content_code' => 'grand_exchange']);
    }
}
