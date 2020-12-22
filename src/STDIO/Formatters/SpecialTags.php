<?php

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Interfaces\Formatter, Styles, Terminal
};

class SpecialTags implements Formatter {

    /** @var Styles */
    protected $styles;

    /** @var Terminal */
    protected $term;

    /**
     * {@inheritdoc}
     * @suppress PhanImpossibleCondition
     */
    public function format(string $message): string {

        $message = preg_replace_callback('/([<](?:\\\)*(?P<tag>\w+)(?:\s+(?P<extra>.*?))?[>])/', function($matches) {

            $params = [];

            $extra = $matches['extra'] ?? null;
            $tag = $matches['tag'];

            if (is_string($extra)) {
                if (preg_match_all('/(\w+)\=[\'\"](\w+)[\'\"]/', $extra, $out) !== false) {
                    list(, $keys, $values) = $out;
                    $params = array_combine($keys, $values);
                }
            }

            $repeat = (isset($params['repeat'])and is_numeric($params['repeat'])) ? (int) $params['repeat'] : 1;
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
            return $matches[0];
        }, $message);


        return $message;
    }

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

    public function setStyles(Styles $styles) {
        $this->styles = $styles;
    }

    public function setTerminal(Terminal $terminal) {
        $this->term = $terminal;
    }

}
