<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Entity\Character;
use Application\Enum\SkillType;
use Application\Infrastructure\Helper\ApiErrorTrait;
use Velkuns\ArtifactsMMO\Client\ResourcesClient;
use Velkuns\ArtifactsMMO\VO\Resource;

class ResourceRepository
{
    use ApiErrorTrait;

    public function __construct(private readonly ResourcesClient $client) {}

    /**
     * @param array{min_level?:int, max_level?:int, skill?:string, drop?:string, page?:int, size?:int} $query
     * @return Resource[]
     * @throws \Throwable
     */
    public function findAll(array $query = []): array
    {
        try {
            return $this->client->getAllResources($query);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @throws \Throwable
     */
    public function find(string $code): Resource
    {
        try {
            return $this->client->getResource($code);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return Resource[]
     * @throws \Throwable
     */
    public function findAllByDrop(string $code): array
    {
        return $this->findAll(['drop' => $code]);
    }

    /**
     * @throws \Throwable
     */
    public function findBestByDrop(string $dropCode, Character $character): Resource
    {
        $bestRate     = \PHP_INT_MAX;
        $bestResource = null;

        $resources = $this->findAllByDrop($dropCode);

        foreach ($resources as $resource) {
            $skillType = SkillType::from($resource->skill);
            if (!$character->skills->hasLevel($skillType, $resource->level)) {
                continue;
            }

            foreach ($resource->drops as $drop) {
                if ($drop->code !== $dropCode) {
                    continue;
                }

                if ($bestResource === null || $drop->rate < $bestRate) {
                    $bestRate     = $drop->rate;
                    $bestResource = $resource;
                }
            }
        }

        if ($bestResource === null) {
            throw new \UnexpectedValueException('Unable to find best resource!');
        }

        return $bestResource;
    }
}
