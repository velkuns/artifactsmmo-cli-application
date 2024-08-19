<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character\Task;

use Application\Infrastructure\Client\CharacterRepository;
use Application\Script\Common\CharacterTrait;
use Application\Task\Task\Crafting as CraftingService;
use Application\Task\Task\Equipping;
use Application\Task\TaskHandler;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
class Crafting extends AbstractScript
{
    use CharacterTrait;

    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly TaskHandler $taskHandler,
        private readonly CraftingService $crafting,
        private readonly Equipping $equipping,
    ) {
        $this->setDescription('Gathering task');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(new Option(shortName: 'n', longName: 'name', description: 'Character name', mandatory: true, hasArgument: true, default: 'natsu'))
                ->add(new Option(shortName: 'i', longName: 'item', description: 'Item code to craft', mandatory: true, hasArgument: true, default: null))
                ->add(new Option(shortName: 'q', longName: 'quantity', description: 'Quantity of the resource to gather', mandatory: true, hasArgument: true, default: 1))
                ->add(new Option(shortName: 'e', longName: 'equip', description: 'Equip the element crafted', mandatory: false, hasArgument: false, default: false))
                ->add(new Option(shortName: 's', longName: 'simulate', description: 'Do a simulation of actions', mandatory: false, hasArgument: false, default: false)),
        );
    }

    public function help(): void
    {
        (new Help(
            substr(self::class, (int) strrpos(self::class, '\\') + 1),
            $this->declaredOptions(),
            $this->output(),
            $this->options(),
        ))->display();
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     * @throws \Throwable
     */
    public function run(): void
    {
        $code     = (string) $this->options()->value('i', 'item');
        $quantity = (int) $this->options()->value('q', 'quantity');
        $doEquip  = (bool) $this->options()->value('e', 'equip');

        $character = $this->getCharacter($this->options(), $this->characterRepository);
        $task      = $this->crafting->createTask($character, $code, $quantity);

        if ($doEquip) {
            $task->enqueueAll($this->equipping->createTask($character, $code, true)); // Add equipments
        }

        $this->taskHandler->handle($character, $task, $this->isSimulation($this->options()));
    }
}
