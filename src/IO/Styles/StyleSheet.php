<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Styles;

use ArrayIterator,
    IteratorAggregate;
use NGSOFT\Tools\{
    Interfaces\StyleInterface, Interfaces\StyleSheetInterface, IO
};
use ReflectionClass;

class StyleSheet implements StyleSheetInterface, IteratorAggregate {

    /** @var array<string,StyleInterface> */
    static protected $defaults = [];

    /** @var array<string,StyleInterface> */
    protected $styles = [];

    public function __construct() {
        if (empty(static::$defaults)) $this->addDefaultStyles();
        $this->styles = self::$defaults;
    }

    private function addDefaultStyles() {

        foreach ((new ReflectionClass(IO::class))->getReflectionConstants() as $const) {

            if (preg_match('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', $const->name, $matches)) {
                list(, $mode, $keyword) = $matches;
                $value = $const->getValue();
                $keyword = strtolower($keyword);
                if ($mode === 'COLOR') {
                    static::$defaults[$keyword] = new Style($keyword, $value);
                    static::$defaults["bg$keyword"] = new Style("bg$keyword", null, $value);
                } else static::$defaults[$keyword] = new Style($keyword, null, null, $value);
            }
        }
        static::$defaults = array_merge(static::$defaults, [
            'error' => static::$defaults['gray']->withBackgroundColor(IO::COLOR_RED)->withName('error'),
            'info' => static::$defaults['green']->withName('info'),
            'comment' => static::$defaults['yellow']->withName('comment'),
            'question' => static::$defaults['black']->withBackgroundColor(IO::COLOR_CYAN)->withName('question'),
            'notice' => static::$defaults['cyan']->withName('notice'),
        ]);
        ksort(self::$defaults);
    }

    /** {@inheritdoc} */
    public function addStyles(StyleInterface ...$styles) {
        foreach ($styles as $style) {
            $name = $style->getName();
            $this->styles[$name] = $style;
        }
        return $this;
    }

    /** {@inheritdoc} */
    public function getStyle(string $keyword) {
        if (!$this->hasStyle($keyword)) return null;
        return $this->styles[$keyword];
    }

    /** {@inheritdoc} */
    public function hasStyle(string $keyword): bool {
        return isset($this->styles[$keyword]);
    }

    /** {@inheritdoc} */
    public function getStyles() {

        return $this->styles;
    }

    /** {@inheritdoc} */
    public function getKewords(): array {
        return array_keys($this->styles);
    }

    public function getIterator() {
        return new ArrayIterator($this->styles);
    }

}
