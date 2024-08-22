<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Entity\Character;
use Velkuns\ArtifactsMMO\Client\MonstersClient;
use Velkuns\ArtifactsMMO\VO\Monster;

class MonsterRepository
{
    public function __construct(private readonly MonstersClient $client) {}

    /**
     * @param array{min_level?:int, max_level?:int, drop?:string, page?:int, size?:int} $query
     * @return Monster[]
     * @throws \Throwable
     */
    public function findAll(array $query = []): array
    {
        return $this->client->getAllMonsters($query);
    }

    /**
     * @throws \Throwable
     */
    public function find(string $code): Monster
    {
        return $this->client->getMonster($code);
    }

    /**
     * @return Monster[]
     * @throws \Throwable
     */
    public function findAllByDrop(string $code): array
    {
        return $this->findAll(['drop' => $code]);
    }

    /**
     * @throws \Throwable
     */
    public function findBestByDrop(string $dropCode, Character $character): Monster
    {
        $bestRate    = \PHP_INT_MAX;
        $bestMonster = null;

        $monsters = $this->findAllByDrop($dropCode);

        foreach ($monsters as $monster) {
            if (!$character->skills->combat->level >= $monster->level) {
                continue;
            }

            foreach ($monster->drops as $drop) {
                if ($drop->code !== $dropCode) {
                    continue;
                }

                if ($bestMonster === null || $drop->rate < $bestRate) {
                    $bestRate    = $drop->rate;
                    $bestMonster = $monster;
                }
            }
        }

        if ($bestMonster === null) {
            throw new \UnexpectedValueException('Unable to find best resource!');
        }

        return $bestMonster;
    }
}
