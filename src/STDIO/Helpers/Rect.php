<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Outputs\Buffer, Outputs\Renderer, Styles\Style, Styles\Styles
};

/**
 * Draws Rectangles
 */
class Rect implements Renderer
{

    protected const DEFAULT_STYLE = [
        'whiterect',
        Color::BLACK,
        BackgroundColor::GRAY
    ];

    protected Style $style;
    protected Buffer $buffer;

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->buffer = new Buffer();
        $this->styles ??= new Styles();
        $this->style = $this->styles->createStyle(...self::DEFAULT_STYLE);
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function setStyle(Style $style): static
    {
        $this->style = $style;
        return $this;
    }

    public function render(\NGSOFT\Console\Outputs\Output $output): void
    {

    }

}
