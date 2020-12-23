<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\Styles;

final class Style {

    public static $validColors = [
        30, 31, 32, 33, 34, 35, 36, 37, 39,
        90, 91, 92, 93, 94, 95, 96, 97
    ];
    public static $validStyles = [
        0, 1, 2, 3, 4, 7, 8, 9
    ];

    /** @var int[] */
    private $prefix = [];

    /** @var int[] */
    private $suffix = [];

    /** @return Style */
    private function getClone(): Style {
        return clone $this;
    }

    /**
     * Reset Styles
     * @internal
     * @return Style
     */
    public function reset(): Style {
        $this->prefix = [];
        $this->suffix = [];
        return $this;
    }

    /**
     * Get a clone with defined prefix
     * @param array $codes
     * @return Style
     */
    public function withPrefix(array $codes): Style {
        $clone = $this->getClone();
        $clone->prefix = $codes;
        return $clone;
    }

    /**
     * Get a clone with defined suffix
     * @param array $codes
     * @return Style
     */
    public function withSuffix(array $codes): Style {
        $clone = $this->getClone();
        $clone->suffix = $codes;
        return $clone;
    }

    /**
     * Get Prefix as string
     * @return string
     */
    public function getPrefix(): string {
        $prefix = '';
        if (count($this->prefix) > 0) $prefix = sprintf(Styles::ESCAPE . '%s' . Styles::STYLE_SUFFIX, implode(';', $this->prefix));
        return $prefix;
    }

    /**
     * Get Suffix as string
     * @return string
     */
    public function getSuffix(): string {
        $suffix = '';
        if (count($this->suffix) > 0) $suffix = sprintf(Styles::ESCAPE . '%s' . Styles::STYLE_SUFFIX, implode(';', $this->suffix));
        return $suffix;
    }

    /**
     * Format the string using current style
     * @param string $message
     * @return string
     */
    public function format(string $message): string {
        return sprintf("%s%s%s", $this->getPrefix(), $message, $this->getSuffix());
    }

}
