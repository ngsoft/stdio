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
    protected array $replacements = [
        "\s" => " ",
        "\t" => "  ",
        '&gt;' => '>',
        '&lt;' => '<',
    ];

    /** @var array<string,string> */
    protected array $tags = [];

    public function __construct(protected StyleSheet $styles) {
        $this->build();
    }

    protected function build(): void {

        $result = [
            '</>' => $this->styles['reset'],
            '<br>' => "\n",
            '<tab>' => "  "
        ];

        /** @var Style $style */
        foreach ($this->styles as $tagName => $style) {
            $result[sprintf('<%s>', $tagName)] = $style->getPrefix();
            $result[sprintf('</%s>', $tagName)] = $style->getSuffix();
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

    public function __debugInfo(): array {
        return [
            'tags' => $this->tags,
        ];
    }

}
