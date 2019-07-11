<?php

namespace NGSOFT\Tools\Interfaces;

interface StyleInterface {

    /**
     * Set Keyword
     * @param string $name
     * @return static
     */
    public function withName(string $name);

    /**
     * Get Keyword
     * @return string
     */
    public function getName(): string;

    /**
     * Set Foreground color
     * @param int $color
     * @return static
     */
    public function withColor(int $color);

    /**
     * Set Background Color
     * @param int $color
     * @return static
     */
    public function withBackgroundColor(int $color);

    /**
     * Set styles Options
     * @param int ...$options
     * @return static
     */
    public function withStyles(int ...$options);

    /**
     * Apply style to the given text
     * @param string $message
     * @return string
     */
    public function applyTo(string $message): string;
}
