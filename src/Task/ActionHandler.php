<?php

declare(strict_types=1);

namespace Application\Task;

use Application\Task\Action\Action;
use Application\Entity\Character;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

class ActionHandler
{
    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    public function handle(Character $character, Action $action, bool $simulate): Character
    {
        return $action->execute($character, $simulate);
    }
}
