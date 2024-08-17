<?php

declare(strict_types=1);

namespace Application\Exception;

class CooldownException extends \RuntimeException
{
    private float $cooldown;

    public function __construct(
        string $message = "",
        int $code = 0,
        \Throwable|null $previous = null,
    ) {
        parent::__construct($message, $code, $previous);

        preg_match(
            '`\[API-499] Character in cooldown: ([0-9.]+) seconds left`',
            $message,
            $matches,
        );

        $this->cooldown = (float) $matches[1];
    }

    public function getCooldown(): float
    {
        return $this->cooldown;
    }

    public function getCooldownAsInt(): int
    {
        return (int) \ceil($this->cooldown);
    }
}
