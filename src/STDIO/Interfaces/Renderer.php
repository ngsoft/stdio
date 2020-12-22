<?php

namespace NGSOFT\STDIO\Interfaces;

interface Renderer {

    /**
     * Render to the Output
     * @param Output $output
     */
    public function render(Output $output);
}
