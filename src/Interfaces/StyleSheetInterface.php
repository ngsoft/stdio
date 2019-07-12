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
     * @return StyleInterface|null
     */
    public function getStyle(string $keyword);

    /**
     * Check if style exists
     * @param string $keyword
     * @return bool
     */
    public function hasStyle(string $keyword): bool;

    /**
     * Get the registered keywords
     * @return array<string>
     */
    public function getKewords(): array;
}
