<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Task;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\VO\Position;
use Eureka\Component\Console\Option\Options;
use Eureka\Component\Console\Progress\ProgressBar;
use Eureka\Component\Console\Terminal\Terminal;

class Waiter
{
    public function __construct(private readonly Terminal $terminal) {}

    public function wait(int $seconds = 0, string $label = '', string $endLine = \PHP_EOL): void
    {
        $this->render($seconds, $label, $endLine);
    }

    public function waitForCooldown(Character $character, string $label = '', string $endLine = \PHP_EOL): void
    {
        if (!$character->hasCooldown() || $character->getRemainingCooldown() === 0) {
            return;
        }

        $this->render($character->getRemainingCooldown(), $label, $endLine);
    }

    private function render(int $seconds, string $label, string $endLine): void
    {
        while ($seconds > 0) {
            $time = \str_pad($seconds . 's', 5);
            $this->terminal->output()->write("$label ⏱  $time\r");
            sleep(1);
            $seconds--;
        }

        $this->terminal->output()->write("$label ⏱  done !$endLine");
    }
}
