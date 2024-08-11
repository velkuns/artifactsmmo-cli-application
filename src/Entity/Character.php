<?php

declare(strict_types=1);

namespace Application\Entity;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\CharactersClient;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\Body\BodyCrafting;
use Velkuns\ArtifactsMMO\VO\Body\BodyDestination;
use Velkuns\ArtifactsMMO\VO\Character as CharacterVO;

class Character
{
    public function __construct(
        public readonly string $name,
        private readonly CharactersClient $charactersClient,
        private readonly MyClient $myClient,
    ) {}

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function info(): CharacterVO
    {
        return $this->charactersClient->getCharacter($this->name);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     */
    public function move(int $x, int $y): void
    {
        $this->myClient->actionMove($this->name, new BodyDestination($x, $y));
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
}
