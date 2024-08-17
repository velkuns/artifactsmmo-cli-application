<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Character;

use Application\Infrastructure\Client\CharacterRepository;
use Application\Service\Renderer\CharacterRenderer;
use Application\Service\Renderer\ItemRenderer;
use Application\Service\UpgradeEquipmentService;
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
class View extends AbstractScript
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
        private readonly UpgradeEquipmentService $upgradeEquipmentService,
    ) {
        $this->setDescription('Example script');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(
                    new Option(
                        shortName: 'n',
                        longName: 'name',
                        description: 'Character name',
                        mandatory: true,
                        hasArgument: true,
                        default: null,
                    ),
                ),
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
        $name = (string) $this->options()->value('n', 'name');

        if (empty($name)) {
            throw new \UnexpectedValueException('Name is missing!');
        }

        $character = $this->characterRepository->findByName($name);
        $items = $this->upgradeEquipmentService->needUpgradeWeapon($character);

        echo (new CharacterRenderer())->render($character);
        echo (new ItemRenderer())->renderAll($items);
    }
}
