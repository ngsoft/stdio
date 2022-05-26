<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Formatters\Tags\HR, StyleSheet
};
use Stringable,
    ValueError;

class TagFormatter implements FormatterInterface {

    protected const BUILTIN = [
        HR::class,
    ];

    /** @var array<string,string> */
    protected array $replacements = [
        "\s" => " ",
        "\t" => "  ",
        '&gt;' => '>',
        '&lt;' => '<',
    ];

    /** @var array<string,string> */
    protected array $replaceTags = [];
    protected array $tags = [];

    public function __construct(protected StyleSheet $styles) {
        $this->build();
        foreach (self::BUILTIN as $className) {
            $this->addTag(new $className);
        }
    }

    protected function build(): void {

        $result = [
            '</>' => $this->styles['reset'],
            '<br>' => "\n",
            '<tab>' => "  "
        ];

        /** @var \NGSOFT\STDIO\Styles\Style $style */
        foreach ($this->styles as $tagName => $style) {
            $result[sprintf('<%s>', $tagName)] = $style->getPrefix();
            $result[sprintf('</%s>', $tagName)] = $style->getSuffix();
        }
        $this->replaceTags = $result;
    }

    /**
     * Add a custom tag to be managed
     *
     * @param Tag $tag
     * @return static
     */
    public function addTag(Tag $tag): static {
        $this->tags[$tag->getName()] = $tag;
        return $this;
    }

    public function format(string|Stringable|array $messages): string {

        if (!is_array($messages)) $messages = [$messages];
        $result = '';

        foreach ($messages as $message) {
            if ($message instanceof Stringable) $message = $message->__toString();
            if (!is_string($message)) {
                throw new ValueError('Invalid value for message string|\Stringable|string[]|\Stringable[].');
            }



            $message = preg_replace_callback('#<(\/)*([^>]*)>#', function ($matches) {
                list($input, $closing, $contents) = $matches;
                $closing = !empty($closing);

                $params = preg_split('#[\h;]+#', $contents);

                if (!empty($params)) {

                    foreach ($params as $param) {

                        var_dump($param);
                    }
                }



                return $input;
            }, $message);
        }

        return $result;
    }

    public function __debugInfo(): array {
        return [
            'tags' => $this->tags,
        ];
    }

}
