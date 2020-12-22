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

    /** @var array<string,string> */
    protected $tags = [];

    /** {@inheritdoc} */
    public function format(string $message): string {

        $message = str_replace(array_keys($this->tags), array_values($this->tags), $message);
        $message = $this->parseSpecialTags($message);
        $message = strip_tags($message);
        return $message;
    }

    protected function parseSpecialTags(string $message): string {

        return preg_replace_callback('/([<](?:\\\)*(?P<end>\/)*(\w+)[>])/', function($matches) {

            var_dump($matches);

            return $matches[0];
        }, $message);
    }

    public function setTerminal(Terminal $terminal) {
        $this->term = $terminal;
    }

    /** {@inheritdoc} */
    public function setStyles(Styles $styles) {
        $this->styles = $styles;
        $this->build($styles);
    }

    private function build(Styles $styles) {
        $this->tags = [];
        $tags = &$this->tags;

        foreach ($styles as $name => $style) {
            $tags[sprintf('<%s>', $name)] = $style->getPrefix();
            $tags[sprintf('</%s>', $name)] = $style->getSuffix();
        }
    }

}
