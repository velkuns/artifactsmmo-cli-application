<?php

declare(strict_types=1);

namespace Application\Task;

/**
 * @extends \SplQueue<Action\Action>
 */
class Task extends \SplQueue
{
    public function enqueueAll(Task $task): void
    {
        foreach ($task as $action) {
            $this->enqueue($action);
        }
    }
}
