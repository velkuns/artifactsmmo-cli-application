<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Infrastructure\Client\CharacterRepository;
use Application\Service\Waiter;
use Application\Task\Action\Action;
use Application\Task\Action\Buy;
use Application\Task\Action\Craft;
use Application\Task\Action\Gather;
use Application\Entity\Character;
use Application\Task\Action\Move;
use Application\Task\Action\Sell;
use Application\Task\Condition\Condition;
use Application\VO\Item\GeItem;
use Application\VO\Position;
use Eureka\Component\Console\Terminal\Terminal;
use Velkuns\ArtifactsMMO\VO\Map;

class ActionFactory
{
    public function __construct(
        protected readonly Terminal $terminal,
        protected readonly Waiter $waiter,
        protected readonly CharacterRepository $characterRepository,
    ) {}

    /**
     * @template T of Action
     *
     * @param class-string<T> $actionClass
     * @param array<mixed> $arguments
     * @return T
     */
    public function new(
        string $actionClass,
        \Closure $closure,
        array $arguments,
        Condition|null $repeatableCondition = null,
    ): Action {
        return new $actionClass(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $closure,
            $arguments,
            $repeatableCondition,
        );
    }

    public function gather(Character $character, string $resource, Condition|null $repeatableCondition = null): Gather
    {
        return new Gather(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $character->gather(...),
            repeatableCondition: $repeatableCondition,
            context: [$resource],
        );
    }

    public function craft(Character $character, string $code, int $quantity): Craft
    {
        return new Craft(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $character->craft(...),
            [$code, $quantity],
        );
    }

    public function move(Character $character, Position $position, Map $map): Move
    {
        return new Move(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $character->move(...),
            [$position],
            context: [$map],
        );
    }

    public function sell(Character $character, string $code, int $quantity, GeItem $item): Sell
    {
        return new Sell(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $character->sell(...),
            [$code, $quantity],
            context: [$item, $quantity],
        );
    }

    public function buy(Character $character, string $code, int $quantity, GeItem $item): Buy
    {
        return new Buy(
            $this->terminal,
            $this->waiter,
            $this->characterRepository,
            $character->buy(...),
            [$code, $quantity],
            context: [$item, $quantity],
        );
    }

    /**
     * @return Task
     */
    public function newTask(): Task
    {
        return new Task();
    }
}
