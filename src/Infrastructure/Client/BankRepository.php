<?php

declare(strict_types=1);

namespace Application\Infrastructure\Client;

use Application\Infrastructure\Helper\ApiErrorTrait;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Client\MyClient;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;
use Velkuns\ArtifactsMMO\VO\SimpleItem;

class BankRepository
{
    use ApiErrorTrait;

    public function __construct(
        private readonly MyClient $myClient,
    ) {}

    /**
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function getGolds(): int
    {
        $gold = $this->myClient->getBankGolds();

        return $gold->quantity;
    }

    /**
     * @return SimpleItem[]
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function getItem(string $code): array
    {
        try {
            return $this->myClient->getBankItems(['item_code' => $code]);
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

    /**
     * @return SimpleItem[]
     * @throws ArtifactsMMOComponentException
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws JsonException
     * @throws \Exception
     * @throws \Throwable
     */
    public function getItems(): array
    {
        try {
            return $this->myClient->getBankItems();
        } catch (\Throwable $exception) {
            throw $this->handleApiException($exception);
        }
    }

}
