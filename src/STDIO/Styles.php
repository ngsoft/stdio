<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\{
    Styles\Style, Utils\ArrayObject
};

class Styles extends ArrayObject {

    public static $colors = [
        //color => [set, unset]
        'BLACK' => [30, 39],
        'RED' => [31, 39],
        'GREEN' => [32, 39],
        'YELLOW' => [33, 39],
        'BLUE' => [34, 39],
        'MAGENTA' => [35, 39],
        'CYAN' => [36, 39],
        'WHITE' => [37, 39],
        'GRAY' => [90, 39],
        'BRIGHTRED' => [91, 39],
        'BRIGHTGREEN' => [92, 39],
        'BRIGHTYELLOW' => [93, 39],
        'BRIGHTBLUE' => [94, 39],
        'BRIGHTMAGENTA' => [95, 39],
        'BRIGHTCYAN' => [96, 39],
        'BRIGHTWHITE' => [97, 39],
    ];
    public static $styles = [
        //style => [set, unset]
        'RESET' => [0, 0],
        'BOLD' => [1, 22],
        'DIM' => [2, 22],
        'ITALIC' => [3, 23],
        'UNDERLINE' => [4, 24],
        'INVERSE' => [7, 27],
        'HIDDEN' => [8, 28],
        'STRIKETROUGH' => [9, 29]
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
