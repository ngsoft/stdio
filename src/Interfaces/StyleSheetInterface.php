<?php

namespace NGSOFT\Tools\Interfaces;

interface StyleSheetInterface {

    /**
     * Add a Style
     * @param StyleInterface $style
     */
    public function addStyle(StyleInterface $style);

    /**
     * @return array<StyleInterface>
     */
    public function getStyles();
}
