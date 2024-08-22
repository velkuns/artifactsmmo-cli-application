<?php

declare(strict_types=1);

namespace Application\Task\Objective;

use Application\Disciplines\CraftingDiscipline;
use Application\Disciplines\Discipline;
use Application\Entity\Character;
use Application\Infrastructure\Client\BankRepository;
use Application\Infrastructure\Client\ItemRepository;
use Application\Service\Helper\BankTrait;
use Application\Service\Helper\InventoryTrait;
use Application\Service\Renderer\ObjectiveRenderer;
use Application\Task;
use Application\Task\Condition\SkillLevelNotReachCondition;

class Mastering
{
    use BankTrait;
    use InventoryTrait;

    public function __construct(
        private readonly ObjectiveRenderer $renderer,
        private readonly ItemRepository $itemRepository,
        private readonly BankRepository $bankRepository,
        private readonly CraftItem $craftItem,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createObjectives(Character $character, int $levelObjective): Task\Objectives
    {
        $this->renderer->displayTitle('Objective: Level Up');
        $this->renderer->displaySubTitle('Preparing Objective');

        $objectives = new Task\Objectives();

        $main      = $character->disciplines->main;
        $secondary = $character->disciplines->secondary;

        $craftingDiscipline = null;
        if ($main instanceof CraftingDiscipline) {
            $craftingDiscipline = $main;
        } elseif ($secondary instanceof CraftingDiscipline) {
            $craftingDiscipline = $secondary;
        }

        if ($craftingDiscipline !== null) {
            $objectives = $this->handleCraft($objectives, $character, $craftingDiscipline, $levelObjective);
        } else {
            $objectives = $this->handleGather($objectives, $character, $levelObjective);
        }

        $this->renderer->stateInProgress('Computing task...');
        $this->renderer->stateDone();

        return $objectives;
    }

    /**
     * @throws \Throwable
     */
    private function handleCraft(
        Task\Objectives $objectives,
        Character $character,
        Discipline $craftingDiscipline,
        int $levelObjective,
    ): Task\Objectives {
        $skillType = $craftingDiscipline->getSkillType();
        $level     = $character->skills->getLevel($skillType);

        $items = $this->itemRepository->findAllCraftableItem($skillType->value, $level);

        $betterItem = null;
        foreach ($items as $item) {
            if ($item->craft === null) {
                continue;
            }

            $quantity = $this->bankCheckDeposit($this->bankRepository, $item, 5); // We want 5max item in bank
            if ($quantity > 0) {
                $objective = $this->craftItem->createObjective($character, $item->code, $quantity, doStore: true);
                $objectives->enqueue($objective);
            }

            if ($betterItem === null || $item->craft->level > $betterItem->craft->level) {
                $betterItem = $item;
            }
        }

        if ($betterItem === null) {
            throw new \UnexpectedValueException('Unable to find better item to craft');
        }

        $objective = $this->craftItem->createObjective($character, $betterItem->code, 1);
        $objective->setRepeatableCondition(new SkillLevelNotReachCondition($skillType, $levelObjective));
        $objectives->enqueue($objective);

        return $objectives;
    }

    private function handleGather(
        Task\Objectives $objectives,
        Character $character,
        int $levelObjective,
    ): Task\Objectives {
        return $objectives;
    }
}
