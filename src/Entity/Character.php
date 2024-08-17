<?php

declare(strict_types=1);

namespace Application\Entity;

use Application\VO\Cooldown;
use Application\VO\Effect\Attack;
use Application\VO\Effect\Damage;
use Application\VO\Effect\Resistance;
use Application\VO\Equipment;
use Application\VO\Inventory;
use Application\VO\Item\Weapon;
use Application\VO\Position;
use Application\VO\Skills;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\Body\BodyCrafting;
use Velkuns\ArtifactsMMO\VO\Body\BodyDestination;

class Character
{
    public string $skin = '';

    public int $hp = 0;
    public int $haste = 0;
    public int $gold = 0;

    public int $speed = 0;
    public int $criticalStrike = 0;
    public int $stamina = 0;

    public Attack $attack;
    public Resistance $resistance;
    public Damage $damage;

    public Position $position;

    public Skills $skills;

    public Cooldown $cooldown;

    public Weapon|null $weapon;
    public Equipment\Gear $gear;
    public Equipment\Jewels $jewels;

    public Equipment\Artifacts $artifacts;
    public Equipment\Consumables $consumables;
    public Inventory $inventory;

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Throwable
     */
    public function __construct(
        public readonly string $name,
        public readonly MyClient $myClient,
    ) {}

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function move(Position $position): void
    {
        $this->myClient->actionMove($this->name, new BodyDestination($position->x, $position->y));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function gather(): void
    {
        $this->myClient->actionGathering($this->name);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function craft(string $code, int $quantity): void
    {
        $this->myClient->actionCrafting($this->name, new BodyCrafting($code, $quantity));
    }
}
