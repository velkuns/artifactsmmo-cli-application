<?php

declare(strict_types=1);

namespace Application\Service\Renderer;

use Eureka\Component\Console\Color\Bit8RGBColor;
use Eureka\Component\Console\Color\Bit8StandardColor;
use Eureka\Component\Console\Style\Style;
use Eureka\Component\Console\Terminal\Terminal;

trait DisplayTrait
{
    private Terminal $terminal;

    public function displayTitle(string $title): void
    {
        $grey = (new Style())->background(new Bit8RGBColor(2, 2, 2));
        $cyan = (new Style())->background(Bit8StandardColor::Cyan);

        $this->terminal->output()->writeln($grey->apply('  ') . $cyan->apply("  $title  "));
    }

    public function displaySubTitle(string $subTitle): void
    {
        $grey  = (new Style())->background(new Bit8RGBColor(3, 3, 3));
        $green = (new Style())->background(Bit8StandardColor::Green)->color(Bit8StandardColor::Black);

        $this->terminal->output()->writeln($grey->apply('  ') . $green->apply("  $subTitle  "));
    }

    public function displayText(string $content): void
    {
        $this->terminal->output()->writeln($content);
    }

    public function stateInProgress(string $content): void
    {
        $this->terminal->output()->write(" $content");
    }

    public function stateDone(): void
    {
        $this->terminal->output()->writeln('✅');
    }
}
