<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Entity\Character;
use Application\Infrastructure\Helper\ApiErrorTrait;
use Application\Service\Helper\ItemEffectTrait;
use Application\Service\Helper\ItemTrait;
use Application\Service\Waiter;
use Application\VO\Cooldown;
use Application\VO\Effect\Attack;
use Application\VO\Effect\Damage;
use Application\VO\Effect\Resistance;
use Application\VO\Equipment;
use Application\VO\Inventory;
use Application\VO\Position;
use Application\VO\Skill;
use Application\VO\Skills;
use JsonException;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\CharactersClient;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class CharacterRepository
{
    use ApiErrorTrait;
    use ItemTrait;
    use ItemEffectTrait;

    public function __construct(
        private readonly CharactersClient $charactersClient,
        private readonly ItemRepository $itemRepository,
        private readonly MyClient $myClient,
        private readonly Waiter $waiter,
        private readonly ClockInterface $clock,
    ) {}

    /**
     * TO DO:
     * - string $task
     * - string $taskType
     * - int $taskProgress
     * - int $taskTotal
     *
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function findByName(string $name): Character
    {
        $char = $this->charactersClient->getCharacter($name);

        //~ Stats
        $character = new Character($name, $this->myClient, $this->clock, $this->waiter);
        $character->skin  = $char->skin;
        $character->hp    = $char->hp;
        $character->haste = $char->haste;
        $character->gold  = $char->gold;

        //~ Not yet usable, but...
        $character->speed          = $char->speed;
        $character->criticalStrike = $char->criticalStrike;
        $character->stamina        = $char->stamina;

        //~ Attack & Resistance
        $character->attack     = new Attack($char->attackFire, $char->attackEarth, $char->attackWater, $char->attackAir);
        $character->resistance = new Resistance($char->resFire, $char->resEarth, $char->resWater, $char->resAir);
        $character->damage     = new Damage($char->dmgFire, $char->dmgEarth, $char->dmgWater, $char->dmgAir);

        //~ Position
        $character->position   = new Position($char->x, $char->y);

        //~ Cooldown
        $cooldownExpiration = $char->cooldownExpiration !== null ? new \DateTimeImmutable($char->cooldownExpiration) : null;
        $character->cooldown     = new Cooldown($char->cooldown, $cooldownExpiration);

        //~ Skills
        $character->skills                  = new Skills();
        $character->skills->combat          = new Skill($char->level, $char->xp, $char->maxXp, $char->totalXp);
        $character->skills->mining          = new Skill($char->miningLevel, $char->miningXp, $char->miningMaxXp);
        $character->skills->fishing         = new Skill($char->fishingLevel, $char->fishingXp, $char->fishingMaxXp);
        $character->skills->cooking         = new Skill($char->cookingLevel, $char->cookingXp, $char->cookingMaxXp);
        $character->skills->woodCutting     = new Skill($char->woodcuttingLevel, $char->woodcuttingXp, $char->woodcuttingMaxXp);
        $character->skills->weaponCrafting  = new Skill($char->weaponcraftingLevel, $char->weaponcraftingXp, $char->weaponcraftingMaxXp);
        $character->skills->gearCrafting    = new Skill($char->gearcraftingLevel, $char->gearcraftingXp, $char->gearcraftingMaxXp);
        $character->skills->jewelryCrafting = new Skill($char->jewelrycraftingLevel, $char->jewelrycraftingXp, $char->jewelrycraftingMaxXp);

        //~ Equipment
        $character->weapon = $this->itemRepository->findWeapon($char->weaponSlot);
        $character->gear = new Equipment\Gear(
            $this->itemRepository->findShield($char->shieldSlot),
            $this->itemRepository->findHelmet($char->helmetSlot),
            $this->itemRepository->findBodyArmor($char->bodyArmorSlot),
            $this->itemRepository->findLegArmor($char->legArmorSlot),
            $this->itemRepository->findBoots($char->bootsSlot),
        );

        //~ Jewels equipments
        $character->jewels = new Equipment\Jewels(
            [
                1 => $this->itemRepository->findRing($char->ring1Slot),
                2 => $this->itemRepository->findRing($char->ring2Slot),
            ],
            $this->itemRepository->findAmulet($char->ring1Slot),
        );

        //~ Artifacts equipments
        $character->artifacts = new Equipment\Artifacts(
            [
                1 => $this->itemRepository->findArtifact($char->artifact1Slot),
                2 => $this->itemRepository->findArtifact($char->artifact2Slot),
                3 => $this->itemRepository->findArtifact($char->artifact3Slot),
            ],
        );

        //~ Consumables
        $character->consumables = new Equipment\Consumables(
            [
                1 => $this->itemRepository->findConsumable($char->consumable1Slot, $char->consumable1SlotQuantity),
                2 => $this->itemRepository->findConsumable($char->consumable2Slot, $char->consumable2SlotQuantity),
            ],
        );

        //~ Inventory
        $character->inventory = new Inventory($char->inventory ?? [], $char->inventoryMaxItems);

        //~ Task
        // TO DO


        return $character;
    }

}
