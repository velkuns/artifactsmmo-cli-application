<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Common;

use Application\Entity\Character;
use Application\Infrastructure\Client\CharacterRepository;
use Eureka\Component\Console\Option\Options;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

/**
 * @codeCoverageIgnore
 */
trait CharacterTrait
{
    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    protected function getCharacter(Options $options, CharacterRepository $characterRepository): Character
    {
        $name = (string) $options->value('n', 'name');

        if (empty($name)) {
            throw new \UnexpectedValueException('Name is missing!');
        }

        return $characterRepository->findByName($name);
    }

    protected function isSimulation(Options $options): bool
    {
        return (bool) $options->value('', 'simulate');
    }
}
