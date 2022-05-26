<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Styles\Style, StyleSheet
};
use Stringable,
    ValueError;

class TagFormatter implements FormatterInterface {

    /** @var array<string,string> */
    protected $replacements = [
        "\s" => " ",
        "\t" => "  ",
        '&gt;' => '>',
        '&lt;' => '<',
    ];
    protected $tags = [];

    public function __construct(protected StyleSheet $styles) {
        $this->build();
    }

    protected function build(): void {

        $result = [
            '</>' => $this->styles['reset'],
        ];

        /** @var Style $style */
        foreach ($this->styles as $tagName => $style) {

        }




        $this->tags = $result;
    }

    public function format(string|Stringable|array $messages): string {

        if (!is_array($messages)) $messages = [$messages];


        foreach ($messages as $message) {
            if ($message instanceof Stringable) $message = $message->__toString();
            if (!is_string($message)) {
                throw new ValueError('Invalid value for message string|\Stringable|string[]|\Stringable[].');
            }
        }
    }

}
