<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Formatters\Formatter, Outputs\OutputInterface, Outputs\Renderer, Styles\Style, Styles\StyleList
};
use Stringable;

class Helper
{

    protected Style $style = null;

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= new StyleList();
    }

    public function getStyle(): Style
    {
        return $this->style ??= $this->getStyles()->create('');
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }

}
