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

    /**
     * Get a style by keyword
     * @param string $keyword
     * @return StyleInterface
     */
    public function getStyle(string $keyword): StyleInterface;

    /**
     * Check if style exists
     * @param string $keyword
     * @return bool
     */
    public function hasStyle(string $keyword): bool;
}
