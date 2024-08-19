<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Task;
use Application\Entity\Character;
use Application\Infrastructure\Client\MapRepository;
use Application\VO\Position;
use Eureka\Component\Console\Option\Options;
use Eureka\Component\Console\Progress\ProgressBar;

class Waiter
{
    public function wait(int $seconds = 0): void
    {
        $this->render($seconds);
    }

    public function waitForCooldown(Character $character): void
    {
        if (!$character->hasCooldown() || $character->getRemainingCooldown() === 0) {
            return;
        }

        $this->render($character->getRemainingCooldown());
    }

    private function render(int $seconds): void
    {
        $progress = new ProgressBar(new Options(), $seconds, 25);

        while ($seconds > 0) {
            sleep(1);
            $progress->inc();
            echo $progress->render(\str_pad($seconds . 's', 5)) . "\r";
            $seconds--;
        }

        echo $progress->render("done !\n");
    }
}
