<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\{
    Styles\Style, Utils\ArrayObject
};

class Styles extends ArrayObject {

    const DEFAULT_COLORS = [
        'black' => Interfaces\Colors::BLACK,
        'red' => Interfaces\Colors::RED,
        'green' => Interfaces\Colors::GREEN,
        'yellow' => Interfaces\Colors::YELLOW,
        'blue' => Interfaces\Colors::BLUE,
        'purple' => Interfaces\Colors::PURPLE,
        'cyan' => Interfaces\Colors::CYAN,
        'white' => Interfaces\Colors::WHITE,
        'gray' => Interfaces\Colors::GRAY,
        'brightred' => Interfaces\Colors::BRIGHTRED,
        'brightgreen' => Interfaces\Colors::BRIGHTGREEN,
        'brightyellow' => Interfaces\Colors::BRIGHTYELLOW,
        'brightblue' => Interfaces\Colors::BRIGHTBLUE,
        'brightpurple' => Interfaces\Colors::BRIGHTPURPLE,
        'brightcyan' => Interfaces\Colors::BRIGHTCYAN,
        'brightwhite' => Interfaces\Colors::BRIGHTWHITE,
        //custom
        'info' => Interfaces\Colors::GREEN,
        'comment' => Interfaces\Colors::YELLOW,
        'whisper' => Interfaces\Colors::WHITE,
        'shout' => Interfaces\Colors::RED,
        'error' => Interfaces\Colors::BRIGHTRED,
        'notice' => Interfaces\Colors::CYAN,
    ];
    const DEFAULT_FORMATS = [
        'reset' => Interfaces\Formats::RESET,
        'bold' => Interfaces\Formats::BOLD,
        'dim' => Interfaces\Formats::DIM,
        'italic' => Interfaces\Formats::ITALIC,
        'underline' => Interfaces\Formats::UNDERLINE,
        'inverse' => Interfaces\Formats::INVERSE,
        'hidden' => Interfaces\Formats::HIDDEN,
        'striketrough' => Interfaces\Formats::STRIKETROUGH,
    ];

    public static $custom = [
        'error' => [[37, 41], [39, 49]],
        'info' => [[32, 49], [39, 49]],
        'comment' => [[33, 49], [39, 49]],
        'question' => [[30, 46], [39, 49]],
        'notice' => [[36, 49], [39, 49]],
    ];

    public function __construct() {
        parent::__construct($this->build());
    }

    /**
     * Build defaults themes
     * @suppress PhanAccessMethodInternal
     * @return array
     */
    private function build(): array {
        $result = [];
        $style = new Style();

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

}
