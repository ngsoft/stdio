<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\{
    Interfaces\Colors, Interfaces\Formats, Styles\Style, Utils\ArrayObject
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
class Styles extends ArrayObject {

    const DEFAULT_COLORS = [
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
    const DEFAULT_FORMATS = [
        'reset' => Formats::RESET,
        'bold' => Formats::BOLD,
        'dim' => Formats::DIM,
        'italic' => Formats::ITALIC,
        'underline' => Formats::UNDERLINE,
        'inverse' => Formats::INVERSE,
        'hidden' => Formats::HIDDEN,
        'striketrough' => Formats::STRIKETROUGH,
    ];

    public function __construct() {
        parent::__construct($this->build());
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
