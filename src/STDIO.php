<?php

namespace NGSOFT\Tools;

use NGSOFT\Tools\Interfaces\Styles;

class STDIO implements Styles {

    private static $keywords = [];

    public function __construct() {
        if (empty(self::$keywords)) $this->parseKeyWords();
    }

    private function parseKeyWords() {
        foreach ((new \ReflectionClass($this))->getReflectionConstants() as $const) {

            if (preg_match('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', $const->name, $matches)) {
                list(, $mode, $keyword) = $matches;
                self::$keywords[strtolower($keyword)] = $const->getValue();

                if ($mode === 'COLOR') {
                    self::$keywords["bg-" . strtolower($keyword)] = self::COLOR_MODIFIER_BACKGROUND + $const->getValue();
                    self::$keywords["tc-" . strtolower($keyword)] = self::COLOR_MODIFIER_TRUECOLOR + $const->getValue();
                    self::$keywords["bg-tc-" . strtolower($keyword)] = (
                            self::COLOR_MODIFIER_BACKGROUND + self::COLOR_MODIFIER_TRUECOLOR + $const->getValue()
                            );
                }
            }

            //$keyword = preg_replace('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', '$2', $const->name);
            //if ($keyword !== $const->name) self::$keywords[strtolower($keyword)] = $const->getValue();
        }
    }

    public static function create(...$args) {
        return new static(...$args);
    }

}
