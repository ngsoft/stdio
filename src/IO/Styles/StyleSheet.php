<?php

namespace NGSOFT\Tools\IO\Styles;

use NGSOFT\Tools\Interfaces\{
    StyleInterface, StyleSheetInterface
};

class StyleSheet implements StyleSheetInterface {

    /** @var array<StyleInterface> */
    static protected $defaults = [];

    /** @var array<StyleInterface> */
    protected $styles = [];

    public function __construct() {
        if (empty(static::$defaults)) $this->addDefaultStyles();
    }

    private function addDefaultStyles() {
        $d = [];
        foreach ((new ReflectionClass(\NGSOFT\Tools\IO::class))->getReflectionConstants() as $const) {

            if (preg_match('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', $const->name, $matches)) {
                list(, $mode, $keyword) = $matches;
                IO::$keywords[strtolower($keyword)] = $const->getValue();

                if ($mode === 'COLOR') {
                    IO::$keywords["bg-" . strtolower($keyword)] = IO::COLOR_MODIFIER_BACKGROUND + $const->getValue();
                    IO::$keywords["tc-" . strtolower($keyword)] = IO::COLOR_MODIFIER_TRUECOLOR + $const->getValue();
                    IO::$keywords["bg-tc-" . strtolower($keyword)] = (
                            IO::COLOR_MODIFIER_BACKGROUND + IO::COLOR_MODIFIER_TRUECOLOR + $const->getValue()
                            );
                }
            }

            //$keyword = preg_replace('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', '$2', $const->name);
            //if ($keyword !== $const->name) IO::$keywords[strtolower($keyword)] = $const->getValue();
        }
    }

    public function addStyle(StyleInterface $style) {

    }

    public function getStyles() {

    }

}
