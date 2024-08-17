<?php

declare(strict_types=1);

namespace Application\Command;

/**
 * @extends \SplQueue<Action\Action>
 */
class CommandList extends \SplQueue
{
    public function enqueueAll(CommandList $commandList): void
    {
        foreach ($commandList as $command) {
            $this->enqueue($command);
        }
    }

    public function unshiftAll(CommandList $commandList): void
    {
        foreach ($commandList as $command) {
            $this->unshift($command);
        }
    }
}
