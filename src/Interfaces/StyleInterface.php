<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

interface StyleInterface {

    /**
     * Set Keyword
     * @param string $name
     * @return static a new instance with the given name
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
     * @return static a new instance with the given color
     */
    public function withColor(int $color);

    /**
     * Set Background Color
     * @param int $color
     * @return static a new instance with the given background color
     */
    public function withBackgroundColor(int $color);

    /**
     * Set styles Options
     * @param int ...$options
     * @return static A new instance with the given styles
     */
    public function withStyles(int ...$options);

    /**
     * Apply style to the given text
     * @param string $message
     * @return string
     */
    public function applyTo(string $message): string;

    /**
     * Get Prefixed Style
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Get Suffixed Style
     * @return string
     */
    public function getSuffix(): string;
}
