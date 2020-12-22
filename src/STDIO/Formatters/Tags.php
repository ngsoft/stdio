<?php

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Interfaces\Formatter, Styles, Terminal
};

class Tags implements Formatter {

    /** @var Styles */
    protected $styles;

    /** @var Terminal */
    protected $term;

    /** @var SpecialTags */
    protected $specials;

    /** @var array<string,string> */
    protected $tags = [];

    /** @var array<string,string> */
    protected $replacements = [
        "\s" => " ",
        "\t" => "    ",
        '&gt;' => '>',
        '&lt;' => '<'
    ];

    /** {@inheritdoc} */
    public function format(string $message): string {

        $message = $this->specials->format($message);
        $message = str_replace(array_keys($this->tags), array_values($this->tags), $message);
        $message = strip_tags($message); //removes not managed tags
        $message = str_replace(array_keys($this->replacements), array_values($this->replacements), $message);
        return $message;
    }

    public function setTerminal(Terminal $terminal) {
        $this->term = $terminal;
        $this->specials->setTerminal($terminal);
    }

    /** {@inheritdoc} */
    public function setStyles(Styles $styles) {
        $this->styles = $styles;
        $this->specials->setStyles($styles);
        $this->build($styles);
    }

    private function build(Styles $styles) {
        $this->tags = [];
        $tags = &$this->tags;

        foreach ($styles as $name => $style) {
            $tags[sprintf('<%s>', $name)] = $style->getPrefix();
            $tags[sprintf('<\\%s>', $name)] = $style->getPrefix();
            $tags[sprintf('</%s>', $name)] = $style->getSuffix();
        }
    }

    public function __construct() {
        $this->specials = new SpecialTags();
    }

}
