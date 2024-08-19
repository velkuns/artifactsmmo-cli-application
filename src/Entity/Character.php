<?php

declare(strict_types=1);

namespace Application\Entity;

use Application\Enum\Slot;
use Application\Service\Waiter;
use Application\VO\Cooldown;
use Application\VO\Effect\Attack;
use Application\VO\Effect\Damage;
use Application\VO\Effect\Resistance;
use Application\VO\Equipment;
use Application\VO\Inventory;
use Application\VO\Item\Weapon;
use Application\VO\Position;
use Application\VO\Skill\Skills;
use JsonException;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\Body\BodyCrafting;
use Velkuns\ArtifactsMMO\VO\Body\BodyDepositWithdrawGold;
use Velkuns\ArtifactsMMO\VO\Body\BodyDestination;
use Velkuns\ArtifactsMMO\VO\Body\BodyEquip;
use Velkuns\ArtifactsMMO\VO\Body\BodySimpleItem;
use Velkuns\ArtifactsMMO\VO\Body\BodyUnequip;

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
        private readonly MyClient $myClient,
        private readonly ClockInterface $clock,
        private readonly Waiter $waiter,
    ) {}

    public function hasEquipment(Slot $slot): bool
    {
        return match ($slot) {
            Slot::Weapon => $this->weapon !== null,
            Slot::Shield => $this->gear->shield !== null,
            Slot::Helmet => $this->gear->helmet !== null,
            Slot::BodyArmor => $this->gear->bodyArmor !== null,
            Slot::LegArmor => $this->gear->legArmor !== null,
            Slot::Boots => $this->gear->boots !== null,
            Slot::Ring1 => $this->jewels->rings[1] !== null,
            Slot::Ring2 => $this->jewels->rings[2] !== null,
            Slot::Amulet => $this->jewels->amulet !== null,
            Slot::Artifact1 => $this->artifacts->artifacts[1] !== null,
            Slot::Artifact2 => $this->artifacts->artifacts[2] !== null,
            Slot::Artifact3 => $this->artifacts->artifacts[3] !== null,
            Slot::Consumable1 => $this->consumables->consumables[1] !== null,
            Slot::Consumable2 => $this->consumables->consumables[2] !== null,
        };
    }

    public function waitForCooldown(): void
    {
        $this->waiter->waitForCooldown(character: $this);
    }

    public function hasCooldown(): bool
    {
        $cooldownEnd = $this->cooldown->date;

        if ($cooldownEnd === null) {
            return false;
        }

        $now = $this->clock->now();

        return $cooldownEnd > $now;
    }

    /**
     * Get remaining cooldown seconds (rounded up)
     */
    public function getRemainingCooldown(): int
    {
        $cooldownEnd = $this->cooldown->date;

        if ($cooldownEnd === null) {
            return 0;
        }

        $time = $cooldownEnd->getTimestamp() - $this->clock->now()->getTimestamp();

        return max($time, 0);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function equip(Slot $slot, string $code): void
    {
        $this->myClient->actionEquipItem($this->name, new BodyEquip($code, $slot->value));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function unequip(Slot $slot): void
    {
        $this->myClient->actionUnequipItem($this->name, new BodyUnequip($slot->value));
    }

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

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function fight(): void
    {
        $this->myClient->actionFight($this->name);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function depositItem(string $code, int $quantity): void
    {
        $this->myClient->actionDepositBank($this->name, new BodySimpleItem($code, $quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function depositGold(int $quantity): void
    {
        $this->myClient->actionDepositBankGold($this->name, new BodyDepositWithdrawGold($quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function withdrawItem(string $code, int $quantity): void
    {
        $this->myClient->actionWithdrawBank($this->name, new BodySimpleItem($code, $quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function withdrawGold(int $quantity): void
    {
        $this->myClient->actionWithdrawBankGold($this->name, new BodyDepositWithdrawGold($quantity));
    }
}
