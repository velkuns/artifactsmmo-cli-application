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
            404 => new NotFoundException(),
            486 => new ActionInProgressException(),
            490 => new AlreadyAtDestinationException(),
            498 => new CharacterNotFoundException(),
            499 => new CooldownException(),
            default => $exception,
        };
    }

    protected function getHttpCode(string $message): int
    {
        if (!\str_starts_with($message, '[HTTP-')) {
            return 0;
        }

        return (int) \substr($message, 6, 3);
    }
}
