<?php

namespace NGSOFT\Tools\Interfaces;

interface StyleInterface {

    /**
     * Set Keyword
     * @param string $name
     */
    public function setName(string $name);

    /**
     * Get Keyword
     * @return string
     */
    public function getName(): string;

    /**
     * Set Foreground color
     * @param int $color
     */
    public function setColor(int $color);

    /**
     * Set Background Color
     * @param int $color
     */
    public function setBackgroundColor(int $color);

    /**
     * Set styles Options
     * @param int ...$options
     */
    public function setStyleOptions(int ...$options);

    /**
     * Apply style to the given text
     * @param string $message
     * @return string
     */
    public function apply(string $message): string;
}
