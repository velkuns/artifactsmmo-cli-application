<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Infrastructure\Helper\ApiErrorTrait;
use Velkuns\ArtifactsMMO\Client\MapsClient;
use Velkuns\ArtifactsMMO\VO\Map;

class MapRepository
{
    use ApiErrorTrait;

    public function __construct(private readonly MapsClient $client) {}

    /**
     * @return list<Map>
     * @throws \Throwable
     */
    public function findAllResources(): array
    {
        try {
            return $this->client->getAllMaps(['content_type' => 'resource']);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findResource(string $code): array
    {
        try {
            return $this->client->getAllMaps(['content_type' => 'resource', 'content_code' => $code]);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findMonster(string $code): array
    {
        try {
            return $this->client->getAllMaps(['content_type' => 'monster', 'content_code' => $code]);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findBank(): array
    {
        try {
            return $this->client->getAllMaps(['content_type' => 'bank', 'content_code' => 'bank']);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return Map[]
     * @throws \Throwable
     */
    public function findWorkshop(string $code): array
    {
        try {
            return $this->client->getAllMaps(['content_type' => 'workshop', 'content_code' => $code]);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }
}
