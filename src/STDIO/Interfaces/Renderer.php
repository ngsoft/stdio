<?php

namespace NGSOFT\STDIO\Interfaces;

use NGSOFT\STDIO\Styles;

interface Renderer {

    /**
     * Render to the Output
     * @param Output $output
     */
    public function render(Output $output);

    /**
     * Set Styles
     * @param \NGSOFT\STDIO\Interfaces\Styles $styles
     */
    public function setStyles(Styles $styles);
}
