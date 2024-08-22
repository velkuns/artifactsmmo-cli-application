<?php

declare(strict_types=1);

namespace Application\Service\Helper;

use Application\Infrastructure\Client\BankRepository;
use Application\VO\Item\Item;
use Psr\Http\Client\ClientExceptionInterface;
use Velkuns\ArtifactsMMO\Exception\Api\NotFoundException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOClientException;
use Velkuns\ArtifactsMMO\Exception\ArtifactsMMOComponentException;

trait BankTrait
{
    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    protected function countItemInBank(BankRepository $bankRepository, string $code): int
    {
        try {
            $items  = $bankRepository->getItem($code);
            $number = 0;
            foreach ($items as $item) {
                $number += $item->quantity;
            }

            return $number;
        } catch (NotFoundException) {
            return 0;
        }
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    protected function bankCheckDeposit(BankRepository $bankRepository, Item $item, int $itemQuantity): int
    {
        $nbItemInBank  = $this->countItemInBank($bankRepository, $item->code);
        $itemQuantity -= $nbItemInBank;

        return max(0, $itemQuantity);
    }

    /**
     * @throws ArtifactsMMOComponentException
     * @throws \Throwable
     * @throws ClientExceptionInterface
     * @throws ArtifactsMMOClientException
     * @throws \JsonException
     */
    protected function hasItemInBank(BankRepository $bankRepository, string $code): bool
    {
        try {
            $items = $bankRepository->getItem($code);

            return count($items) > 0;
        } catch (NotFoundException) {
            return false;
        }
    }
}
