<?php

declare(strict_types=1);

namespace Application\Entity;

use Application\Enum\Slot;
use Application\Infrastructure\Client\GrandExchangeRepository;
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
use Velkuns\ArtifactsMMO\VO\Body\BodyGETransactionItem;
use Velkuns\ArtifactsMMO\VO\Body\BodySimpleItem;
use Velkuns\ArtifactsMMO\VO\Body\BodyUnequip;

trait ActionTrait
{
    abstract protected function getName(): string;
    abstract protected function getMyClient(): MyClient;
    abstract protected function getGERepository(): GrandExchangeRepository;

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function equip(Slot $slot, string $code): void
    {
        $this->getMyClient()->actionEquipItem($this->getName(), new BodyEquip($code, $slot->value));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function unequip(Slot $slot): void
    {
        $this->getMyClient()->actionUnequipItem($this->getName(), new BodyUnequip($slot->value));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function move(Position $position): void
    {
        $this->getMyClient()->actionMove($this->getName(), new BodyDestination($position->x, $position->y));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function gather(): void
    {
        $this->getMyClient()->actionGathering($this->getName());
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function craft(string $code, int $quantity): void
    {
        $this->getMyClient()->actionCrafting($this->getName(), new BodyCrafting($code, $quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function fight(): void
    {
        $this->getMyClient()->actionFight($this->getName());
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function depositItem(string $code, int $quantity): void
    {
        $this->getMyClient()->actionDepositBank($this->getName(), new BodySimpleItem($code, $quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function depositGold(int $quantity): void
    {
        $this->getMyClient()->actionDepositBankGold($this->getName(), new BodyDepositWithdrawGold($quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function withdrawItem(string $code, int $quantity): void
    {
        $this->getMyClient()->actionWithdrawBank($this->getName(), new BodySimpleItem($code, $quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function withdrawGold(int $quantity): void
    {
        $this->getMyClient()->actionWithdrawBankGold($this->getName(), new BodyDepositWithdrawGold($quantity));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Throwable
     */
    public function sell(string $code, int $quantity): void
    {
        $item = $this->getGERepository()->find($code); // Be sure we have good price before selling it

        $this->getMyClient()->actionGeSellItem($this->getName(), new BodyGETransactionItem($code, $quantity, $item->sellPrice));
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Throwable
     */
    public function buy(string $code, int $quantity): void
    {
        $item = $this->getGERepository()->find($code); // Be sure we have good price before buying it

        $this->getMyClient()->actionGeBuyItem($this->getName(), new BodyGETransactionItem($code, $quantity, $item->buyPrice));
    }
}
