<?php

declare(strict_types=1);

namespace Application\Infrastructure\Helper;

use Application\Exception\ActionInProgressException;
use Application\Exception\AlreadyAtDestinationException;
use Application\Exception\CharacterNotFoundException;
use Application\Exception\CooldownException;
use Application\Exception\NotFoundException;

trait ApiErrorTrait
{
    /**
     * @throws \Throwable
     */
    protected function handleApiException(\Throwable $exception): \Throwable
    {
        $httpCode = $this->getHttpCode($exception->getMessage());

        return match($httpCode) {
            404 => new NotFoundException($exception->getMessage()),
            486 => new ActionInProgressException($exception->getMessage()),
            490 => new AlreadyAtDestinationException($exception->getMessage()),
            498 => new CharacterNotFoundException($exception->getMessage()),
            499 => new CooldownException($exception->getMessage()),
            default => $exception,
        };
    }

    protected function getHttpCode(string $message): int
    {
        if (!\str_starts_with($message, '[API-')) {
            return 0;
        }

        return (int) \substr($message, 5, 3);
    }
}
