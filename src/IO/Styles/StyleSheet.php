<?php

namespace NGSOFT\Tools\IO\Styles;

use NGSOFT\Tools\{
    Interfaces\StyleInterface, Interfaces\StyleSheetInterface, IO
};
use ReflectionClass;

class StyleSheet implements StyleSheetInterface {

    /** @var array<StyleInterface> */
    static protected $defaults = [];

    /** @var array<StyleInterface> */
    protected $styles = [];

    public function __construct() {
        if (empty(static::$defaults)) $this->addDefaultStyles();
        $this->styles = self::$defaults;
    }

    private function addDefaultStyles() {
        $d = [];
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
    public function getStyle(string $keyword): StyleInterface {
        if (!$this->hasStyle($keyword)) $this->styles[$keyword] = new Style($keyword);
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

}
