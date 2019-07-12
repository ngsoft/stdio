<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Styles;

use InvalidArgumentException;
use NGSOFT\Tools\{
    Interfaces\StyleInterface, IO, IO\Terminal
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

    /** @var bool Color support detection */
    protected static $hasColors;

    /** @var string */
    protected $name = "";

    /** @var array<int>|null */
    protected $color;

    /** @var array<int>|null */
    protected $bg;

    /** @var array */
    protected $opts = [];

    /** @var string */
    protected $prefix = "";

    /** @var string */
    protected $suffix = "";

    /** {@inheritdoc} */
    public function getPrefix(): string {
        //do not display escaped chars if ansi is not supported
        if (self::$hasColors) $this->update();
        return $this->prefix;
    }

    /** {@inheritdoc} */
    public function getSuffix(): string {
        if (self::$hasColors) $this->update();
        return $this->suffix;
    }

    /** {@inheritdoc} */
    public function applyTo(string $message): string {
        $prefix = $this->getPrefix();
        if (!empty($prefix)) return sprintf('%s%s%s', $prefix, $message, $this->getSuffix());
        return $message;
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function withBackgroundColor(int $color) {
        return $this->getClone()->setBg($color);
    }

    /** {@inheritdoc} */
    public function withColor(int $color) {
        return $this->getClone()->setColor($color);
    }

    /** {@inheritdoc} */
    public function withName(string $name) {
        return $this->getClone()->setName($name);
    }

    /** {@inheritdoc} */
    public function withStyles(int...$options) {
        if (count($options) > 0) return $this->getClone()->setOpts(... $options);
        return $this;
    }

    /** {@inheritdoc} */
    private function assertValidColor(int $color) {
        if (!isset(self::$sets["colors"][$color])) {
            throw new InvalidArgumentException("Invalid Color Supplied");
        }
    }

    /** {@inheritdoc} */
    private function assertValidStyle(int $style) {
        if (!isset(self::$sets["styles"][$style])) {
            throw new InvalidArgumentException("Invalid Style Supplied");
        }
    }

    /**
     * @param string $name
     * @param int|null $color
     * @param int|null $background_color
     * @param int ...$options
     */
    public function __construct(string $name, int $color = null, int $background_color = null, int...$options) {
        $this->setName($name);
        $color !== null and $this->setColor($color);
        $background_color !== null and $this->setBg($background_color);
        count($options) > 0 and $this->setOpts(...$options);
        if (self::$hasColors === null) self::$hasColors = (new Terminal())->hasColorSupport();
    }

    /**
     * @param string $name
     * @return static
     * @throws InvalidArgumentException
     */
    private function setName(string $name) {
        if (preg_match('/^\w+$/', $name)) {
            $this->name = strtolower($name);
        } else throw new InvalidArgumentException("Invalid Name $name");
        $this->prefix = "";
        return $this;
    }

    /**
     * @param int $color
     * @return static
     */
    private function setColor(int $color) {
        $this->assertValidColor($color);
        $this->color = self::$sets["colors"][$color];
        $this->prefix = "";
        return $this;
    }

    /**
     * @param int $color
     * @return static
     */
    private function setBg(int $color) {
        $this->assertValidColor($color);
        $this->bg = array_map(function (int $c) { return $c + 10; }, self::$sets["colors"][$color]);
        $this->prefix = "";
        return $this;
    }

    /**
     * @param int ...$options
     * @return static
     */
    private function setOpts(int ...$options) {
        if (count($options) > 0) $this->opts = [];
        foreach ($options as $opt) {
            $this->assertValidStyle($opt);
            $this->opts[] = self::$sets["styles"][$opt];
        }
        $this->prefix = "";
        return $this;
    }

    /**
     * Updates prefix and suffix
     */
    protected function update() {
        if (!empty($this->prefix)) return;
        $this->prefix = $this->suffix = "";
        $prefix = $suffix = [];
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
            $suffix [] = $opt[1];
        }
        if (count($prefix) > 0) {
            $this->prefix = sprintf("\033[%sm", implode(';', $prefix));
            $this->suffix = sprintf("\033[%sm", implode(';', $suffix));
        }
    }

    protected function getClone(): self {
        return clone $this;
    }

}
