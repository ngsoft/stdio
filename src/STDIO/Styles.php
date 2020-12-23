<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\{
    Styles\Style, Utils\ArrayObject
};

class Styles extends ArrayObject {

    public static $colors = [
        //color => [set, unset]
        'black' => [30, 39],
        'red' => [31, 39],
        'green' => [32, 39],
        'yellow' => [33, 39],
        'blue' => [34, 39],
        'magenta' => [35, 39],
        'cyan' => [36, 39],
        'white' => [37, 39],
        'gray' => [90, 39],
        'brightred' => [91, 39],
        'brightgreen' => [92, 39],
        'brightyellow' => [93, 39],
        'brightblue' => [94, 39],
        'brightmagenta' => [95, 39],
        'brightcyan' => [96, 39],
        'brightwhite' => [97, 39],
    ];
    public static $styles = [
        //style => [set, unset]
        'reset' => [0, 0],
        'bold' => [1, 22],
        'dim' => [2, 22],
        'italic' => [3, 23],
        'underline' => [4, 24],
        'inverse' => [7, 27],
        'hidden' => [8, 28],
        'striketrough' => [9, 29]
    ];
    public static $custom = [
        'error' => [[37, 41], [39, 49]],
        'info' => [[32, 49], [39, 49]],
        'comment' => [[33, 49], [39, 49]],
        'question' => [[30, 46], [39, 49]],
        'notice' => [[36, 49], [39, 49]],
    ];
    public static $replacements = [
        "\t" => "    ",
        "\s" => " ",
    ];

    const BG_COLOR_MODIFIER = 10;
    const TRUE_COLOR_MODIFIER = 60;
    //const ESCAPE = "\u001B[";
    const ESCAPE = "\033[";
    const STYLE_SUFFIX = "m";
    const CLEAR_LINE = self::ESCAPE . "2K";
    const CLEAR_START_LINE = self::ESCAPE . "1K";
    const CLEAR_END_LINE = self::ESCAPE . "K";
    const LINE_BREAK = "\n";
    const RETURN = "\r";

    public function __construct() {
        parent::__construct($this->build());
    }

    /**
     * Build defaults themes
     * @return array
     */
    private function build(): array {
        $result = [];
        $style = new Style();

        foreach (self::$colors as $name => $params) {
            $name = strtolower($name);
            list($set, $unset) = $params;
            //build colors
            $result[$name] = $style->withPrefix([$set])->withSuffix([$unset]);
            //build bgcolors
            $result["bg$name"] = $style->withPrefix([$set + 10])->withSuffix([$unset + 10]);
        }

        foreach (self::$styles as $name => $params) {
            $name = strtolower($name);
            list($set, $unset) = $params;
            //build styles
            $result[$name] = $style->withPrefix([$set])->withSuffix([$unset]);
        }
        foreach (self::$custom as $name => $params) {
            $name = strtolower($name);
            list($set, $unset) = $params;
            $result[$name] = $style->withPrefix($set)->withSuffix($unset);
        }

        return $result;
    }

}
