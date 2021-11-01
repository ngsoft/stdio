<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use ArrayAccess,
    BadMethodCallException,
    Countable,
    InvalidArgumentException,
    IteratorAggregate;
use NGSOFT\STDIO\{
    Interfaces\Colors, Interfaces\Formats, Styles\Style
};

/**
 * @property Style $black
 * @property Style $red
 * @property Style $green
 * @property Style $yellow
 * @property Style $blue
 * @property Style $purple
 * @property Style $cyan
 * @property Style $white
 * @property Style $gray
 * @property Style $brightred
 * @property Style $brightgreen
 * @property Style $brightyellow
 * @property Style $brightblue
 * @property Style $brightpurple
 * @property Style $brightcyan
 * @property Style $brightwhite
 * @property Style $info
 * @property Style $comment
 * @property Style $whisper
 * @property Style $shout
 * @property Style $error
 * @property Style $notice
 * @property Style $reset
 * @property Style $bold
 * @property Style $dim
 * @property Style $italic
 * @property Style $underline
 * @property Style $inverse
 * @property Style $hidden
 * @property Style $striketrough
 */
final class Styles implements IteratorAggregate, Countable, ArrayAccess {

    protected const DEFAULT_COLORS = [
        'black' => Colors::BLACK,
        'red' => Colors::RED,
        'green' => Colors::GREEN,
        'yellow' => Colors::YELLOW,
        'blue' => Colors::BLUE,
        'purple' => Colors::PURPLE,
        'cyan' => Colors::CYAN,
        'white' => Colors::WHITE,
        'gray' => Colors::GRAY,
        'brightred' => Colors::BRIGHTRED,
        'brightgreen' => Colors::BRIGHTGREEN,
        'brightyellow' => Colors::BRIGHTYELLOW,
        'brightblue' => Colors::BRIGHTBLUE,
        'brightpurple' => Colors::BRIGHTPURPLE,
        'brightcyan' => Colors::BRIGHTCYAN,
        'brightwhite' => Colors::BRIGHTWHITE,
        //custom
        'info' => Colors::GREEN,
        'comment' => Colors::YELLOW,
        'whisper' => Colors::WHITE,
        'shout' => Colors::RED,
        'error' => Colors::BRIGHTRED,
        'notice' => Colors::CYAN,
    ];
    protected const DEFAULT_FORMATS = [
        'reset' => Formats::RESET,
        'bold' => Formats::BOLD,
        'dim' => Formats::DIM,
        'italic' => Formats::ITALIC,
        'underline' => Formats::UNDERLINE,
        'inverse' => Formats::INVERSE,
        'hidden' => Formats::HIDDEN,
        'striketrough' => Formats::STRIKETROUGH,
    ];

    /** @var array<string,Style> */
    private $styles = [];

    /**
     * @staticvar array $build
     */
    public function __construct() {
        static $build;
        $build = $build ?? $this->build(Terminal::create()->hasColorSupport());
        $this->styles = $build;
    }

    /**
     * Adds a Custom Style
     * @param string $name name to use to access it (Styles and STDIO)
     * @param Style $style
     * @return static
     */
    public function addStyle(string $name, Style $style) {
        $this[$name] = $style->withName($name);
        return $this;
    }

    /**
     * Build defaults themes
     * @suppress PhanAccessMethodInternal
     * @return array
     */
    private function build(bool $supportsColors): array {
        $result = [];
        $style = new Style($supportsColors);

        foreach (self::DEFAULT_COLORS as $name => $code) {
            //color
            $result[$name] = $style
                    ->withName($name)
                    ->withColor($code)
                    ->compile();

            //bgcolor
            $result["bg$name"] = $style
                    ->withName("bg$name")
                    ->withBackground($code)
                    ->compile();
        }

        foreach (self::DEFAULT_FORMATS as $name => $code) {
            $result[$name] = $style
                    ->withName($name)
                    ->withFormats([$code])
                    ->compile();
        }
        return $result;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function offsetExists($offset) {

        return
                is_string($offset) ?
                false :
                isset($this->styles[$offset]);
    }

    /** {@inheritdoc} */
    public function &offsetGet($offset) {
        // find style using Color::COLOR
        if (is_int($offset)) {
            $color = array_search($offset, self::DEFAULT_COLORS);
            $offset = $color === false ? null : $color;
        }

        if (is_null($offset)) $result = null;
        else $result = $this->styles[$offset] ?? null;
        return $result;
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value) {
        if ($value instanceof Style and is_string($offset)) {
            $this->styles[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->styles[$offset]);
    }

    public function count() {
        return count($this->styles);
    }

    /**
     * @return \Generator<string,Style>
     */
    public function getIterator() {
        foreach ($this->styles as $name => $style) yield $name => $style;
    }

    ////////////////////////////   Magic Methods   ////////////////////////////

    /**
     * Access Format directly
     *
     * @param string $method
     * @param array $arguments
     * @return string
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function __call($method, $arguments) {
        if (!isset($arguments[0]) or!is_string($arguments[0])) throw new InvalidArgumentException('No argument given for ' . get_class($this) . '::' . $method . '()');
        elseif (!isset($this->styles[$method])) throw new BadMethodCallException("Method " . get_class($this) . "$method() does not exists");
        return $this->styles[$method]->format($arguments[0]);
    }

    /** {@inheritdoc} */
    public function __clone() {
        foreach ($this->styles as $name => $style) {
            $this->styles[$name] = clone $style;
        }
    }

    /** {@inheritdoc} */
    public function &__get($name) {
        $value = $this->offsetGet($name);
        return $value;
    }

    /** {@inheritdoc} */
    public function __isset($name) {
        return $this->offsetExists($name);
    }

    /** {@inheritdoc} */
    public function __set($name, $value) {
        $this->offsetSet($name, $value);
    }

    /** {@inheritdoc} */
    public function __unset($name) {
        $this->offsetUnset($name);
    }

    /** {@inheritdoc} */
    public function __debugInfo() {
        return [];
    }

}
