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

    /** @var Tag[] */
    protected $formatTags = [];

    /** @var array<string,string> */
    protected $replacements = [
        "\s" => " ",
        "\t" => "    ",
        '&gt;' => '>',
        '&lt;' => '<'
    ];

    /** @param ?Styles $styles */
    public function __construct(Styles $styles = null) {
        $this->styles = $styles ?? new Styles();
        $this->term = Terminal::create();
    }

    /** {@inheritdoc} */
    public function format(string $message): string {
        if (empty($this->tags)) $this->build();
        $message = $this->formatExtraTags($message);
        $message = str_replace(array_keys($this->tags), array_values($this->tags), $message);
        $message = strip_tags($message); //removes not managed tags
        $message = str_replace(array_keys($this->replacements), array_values($this->replacements), $message);
        return $message;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Add Spaces
     * @param int $repeat
     * @return string
     */
    public function space(int $repeat = 1): string {
        $repeat = max(1, $repeat);
        return str_repeat(" ", $repeat);
    }

    /**
     * Adds Separator
     * @param string $char
     * @return string
     */
    public function hr(string $char = "-"): string {
        $width = $this->term->width;
        return sprintf("\n  %s  \n", str_repeat($char, $width - 4));
    }

    /**
     * Adds Line Break
     * @param int $repeat
     * @return string
     */
    public function br(int $repeat = 1): string {
        $repeat = max(1, $repeat);
        return str_repeat("\n", $repeat);
    }

    /**
     * Adds Tabs
     * @param int $repeat
     * @return string
     */
    public function tab(int $repeat = 1): string {
        $repeat = max(1, $repeat);
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

    /**
     *
     */
    private function formatExtraTags(string $message): string {


        $message = str_replace(['<\\', '\\>'], ['</', '/>'], $message);

        $message = preg_replace_callback('/[<](?P<closing>[\/])*(?P<tag>\w+)(?:\s+(?P<extra>.*?))?(?:[\/])*[>]/', function ($matches) {
            $input = $matches[0];

            $closing = !empty($matches['closing']);

            $params = [];

            $extra = $matches['extra'] ?? null;
            $tag = $matches['tag'];

            if (is_string($extra)) {
                if (preg_match_all('/(\w+)\=[\'\"]*([\w\-]+)[\'\"]*/', $extra, $out) !== false) {
                    list(, $keys, $values) = $out;
                    $params = array_combine($keys, $values);
                }
            }

            var_dump($params);

            $repeat = isset($params['repeat']) && is_numeric($params['repeat']) ? intval($params['repeat']) : 1;
            $char = isset($params['char']) ? $params['char'] : '-';

            switch ($tag) {
                case "space":
                    return $this->space($repeat);
                case "tab":
                    return $this->tab($repeat);
                case "br":
                    return $this->br($repeat);
                case "hr":
                    return $this->hr($char);
            }
            return $input;
        }, $message);

        return $message;
    }

}
