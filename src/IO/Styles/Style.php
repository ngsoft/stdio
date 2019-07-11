<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Styles;

use InvalidArgumentException;
use NGSOFT\Tools\{
    Interfaces\StyleInterface, IO
};

class Style implements StyleInterface {

    const STYLE_PREFIX = "\033[";
    const STYLE_SUFFIX = "m";

    protected static $sets = [
        "colors" => [
            IO::COLOR_BLACK => [30, 39],
            IO::COLOR_RED => [31, 39],
            IO::COLOR_GREEN => [32, 39],
            IO::COLOR_YELLOW => [33, 39],
            IO::COLOR_BLUE => [34, 39],
            IO::COLOR_MAGENTA => [35, 39],
            IO::COLOR_CYAN => [36, 39],
            IO::COLOR_GRAY => [37, 39],
            IO::COLOR_DEFAULT => [39, 39],
        ],
        "styles" => [
            IO::STYLE_BRIGHT => [1, 22],
            IO::STYLE_UNDERSCORE => [4, 24],
            IO::STYLE_BLINK => [5, 25],
            IO::STYLE_REVERSE => [7, 27],
            IO::STYLE_CONCEAL => [8, 28],
        ]
    ];

    /** @var string */
    private $name = "";

    /** @var array<int>|null */
    private $color;

    /** @var array<int>|null */
    private $bg;

    /** @var array<int> */
    private $opts = [];

    public function applyTo(string $message): string {
        $prefix = [];
        $suffix = [];

        if (is_array($this->color)) {
            $prefix[] = $this->color[0];
            $suffix[] = $this->color[1];
        }
        if (is_array($this->bg)) {
            $prefix [] = $this->bg[0];
            $suffix [] = $this->bg[1];
        }
        foreach ($this->opts as $opt) {
            $prefix[] = $opt[0];
            $suffix[] = $opt[1];
        }
        if (count($prefix) === 0) return $message;
        return sprintf("\033[%sm%s\033[%sm", implode(';', $prefix), implode(';', $suffix));
    }

    public function getName(): string {
        return $this->name;
    }

    public function withBackgroundColor(int $color) {
        $this->assertValidColor($color);

        $this->bg = array_map(function (int $c) { return $c + 10; }, self::$sets["colors"][$color]);
        return $this;
    }

    public function withColor(int $color) {
        $this->assertValidColor($color);
        $this->bg = self::$sets["colors"][$color];
        return $this;
    }

    public function withName(string $name) {
        if (preg_match('/^\w+$/', $name)) {
            $this->name = strtolower($name);
        } else throw new InvalidArgumentException("Invalid Name $name");
    }

    public function withStyles(int...$options) {
        foreach ($options as $opt) {
            $this->assertValidStyle($opt);
            $this->opts[] = self::$sets["styles"][$opt];
        }
        return $this;
    }

    private function assertValidColor(int $color) {
        if (!isset(self::$sets["colors"][$color])) {
            throw new InvalidArgumentException("Invalid Color Supplied");
        }
    }

    private function assertValidStyle(int $style) {
        if (!isset(self::$sets["styles"][$style])) {
            throw new InvalidArgumentException("Invalid Style Supplied");
        }
    }

}
