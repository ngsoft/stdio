<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Interfaces\Formatter, Styles, Terminal
};

class TagFormatter implements Formatter {

    /** @var Styles */
    protected $styles;

    /** @var Terminal */
    protected $term;

    /** @var array<string,string> */
    protected $tags = [];

    /** @var array<string,string> */
    protected $replacements = [
        "\s" => " ",
        "\t" => "    ",
        '&gt;' => '>',
        '&lt;' => '<'
    ];

    public function __construct(Styles $styles = null) {
        $this->styles = $styles ?? new Styles();
        $this->term = Terminal::create();
        $this->build();
    }

    public function format(string $message): string {

    }

    /**
     * Build the tags
     */
    private function build() {
        $this->tags = [];
        $styles = $this->styles;
        $tags = &$this->tags;
        $tags['</>'] = '';
        foreach ($styles as $name => $style) {
            $tags[sprintf('<%s>', $name)] = $style->getPrefix();
            $tags[sprintf('<\\%s>', $name)] = $style->getPrefix();
            $tags[sprintf('</%s>', $name)] = $style->getSuffix();
        }
    }

}
