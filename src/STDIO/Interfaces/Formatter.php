<?php

namespace NGSOFT\STDIO\Interfaces;

interface Formatter {

    /**
     * Formats a message.
     */
    public function format(string $message): string;

    /**
     * Add a Style to the formatter
     * @param Style $style
     * @param string $name
     */
    public function addStyle(Style $style, string $name);

    /**
     * Get Named Style from the Formatter
     * @param string $name
     * @return Style|null
     */
    public function getStyle(string $name): ?Style;

    /**
     * Get All Styles from the formatter
     * @return array<string,Style>
     */
    public function getStyles(): array;
}
