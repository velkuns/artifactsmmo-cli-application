<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Command;
use Application\Entity\Character;
use Application\Enum\SkillType;
use Application\Infrastructure\Client\ItemRepository;
use Application\Infrastructure\Client\MapRepository;
use Application\Service\Helper\MapTrait;
use Application\Service\Helper\MathTrait;
use Application\VO\Item\Item;
use Application\VO\Position;

class Crafting
{
    use MapTrait;

    public function __construct(
        private readonly MapRepository $mapRepository,
        private readonly ItemRepository $itemRepository,
        private readonly Command\CommandFactory $commandFactory,
    ) {}

    /**
     * @throws \Throwable
     */
    public function createCommands(Character $character, string $code, int $quantity): Command\CommandList
    {
        $commands = $this->commandFactory->newList();

        $item = $this->itemRepository->findItem(Item::class, $code);

        if ($item === null || $item->craft === null) {
            return $commands;
        }

        try {
            $skillType = SkillType::from($item->craft->skill);
        } catch (\TypeError) {
            return $commands;
        }

        //~ Handle move if necessary
        $commands = $this->handleMove($character, $this->mapRepository->findWorkshop($skillType->value), $commands);

        //~ Then enqueue main action
        $command = $this->commandFactory->new(Command\Action\Craft::class, $character->craft(...), [$code, $quantity]);
        $commands->enqueue($command);

        return $commands;
    }
}
