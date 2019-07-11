<?php

namespace NGSOFT\Tools\Interfaces;

interface StyleSheetInterface {

    /**
     * Add a Style
     * @param StyleInterface ...$style
     * return static;
     */
    public function addStyles(StyleInterface ...$style);

    /**
     * @return array<string,StyleInterface>
     */
    public function getStyles();
}
