<?php

declare(strict_types=1);

namespace Application\VO\Equipment;

use Application\VO\Item;

class Artifacts
{
    /**
     * @param array{1: Item\Artifact|null, 2: Item\Artifact|null, 3: Item\Artifact|null} $artifacts
     */
    public function __construct(public array $artifacts) {}
}
