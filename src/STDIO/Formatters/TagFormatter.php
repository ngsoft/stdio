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

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Add Spaces
     * @param int $repeat
     * @return string
     */
    public function space(int $repeat = 1): string {
        return str_repeat(" ", $repeat);
    }

    /**
     * Adds Separator
     * @param string $char
     * @return string
     */
    public function hr(string $char = "-"): string {

        $width = $this->term->width;
        return sprintf("\n%s\n", str_repeat($char, $width));
    }

    /**
     * Adds Line Break
     * @param int $repeat
     * @return string
     */
    public function br(int $repeat = 1): string {
        return str_repeat("\n", $repeat);
    }

    /**
     * Adds Tabs
     * @param int $repeat
     * @return string
     */
    public function tab(int $repeat = 1): string {
        return str_repeat("\t", $repeat);
    }

    /**
     * Build the tags
     */
    private function build() {
        $this->tags = [];
        $styles = $this->styles;
        $tags = &$this->tags;
        $tags['</>'] = $styles->unset->getSuffix();
        foreach ($styles as $name => $style) {
            $tags[sprintf('<%s>', $name)] = $style->getPrefix();
            $tags[sprintf('<\\%s>', $name)] = $style->getPrefix();
            $tags[sprintf('</%s>', $name)] = $style->getSuffix();
        }
    }

}
