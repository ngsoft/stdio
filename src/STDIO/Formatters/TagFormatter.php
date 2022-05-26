<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Formatters\Tags\Format, Formatters\Tags\HR, Styles\Style, StyleSheet
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
    protected Tag $defaultTag;
    protected StyleSheet $styles;

    public function __construct(StyleSheet $styles) {
        $this->styles = $styles;
        $this->setDefaultTag(new Format($styles));
        foreach (self::BUILTIN as $className) {
            $this->addTag(new $className($this->styles));
        }
        $this->build();
    }

    protected function build(): void {

        $result = [
            '</>' => $this->styles['reset']->getPrefix(),
            '<br>' => "\n",
            '<tab>' => "  "
        ];

        /** @var Style $style */
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

    /**
     * Default Managed Tag
     *
     * @return Tag
     */
    public function getDefaultTag(): Tag {
        return $this->defaultTag;
    }

    public function setDefaultTag(Tag $defaultTag): static {
        $this->addTag($defaultTag);
        $this->defaultTag = $defaultTag;
        return $this;
    }

    public function format(string|Stringable|array $messages): string {

        if (!is_array($messages)) $messages = [$messages];
        $result = '';

        foreach ($messages as $message) {
            if ($message instanceof Stringable) $message = $message->__toString();
            if (!is_string($message)) {
                throw new ValueError('Invalid value for message string|\Stringable|string[]|\Stringable[]: ' . get_debug_type($message));
            }

            var_dump(array_values($this->replaceTags));

            $message = str_replace(array_keys($this->replaceTags), array_values($this->replaceTags), $message);

            $message = preg_replace_callback('#<(\/)*([^>]*)>#', function ($matches) {
                list($input, $closing, $contents) = $matches;
                $closing = !empty($closing);

                if (!empty($contents)) {
                    $params = preg_split('#[\h;]+#', $contents);

                    if (!empty($params)) {


                        $tagName = strtolower($params[0]);

                        /** @var Tag $tagInstance */
                        $tagInstance = $this->tags[$tagName] ?? $this->defaultTag;

                        $implParams = [];

                        foreach ($params as $param) {
                            if (preg_match('#([\w\-]+)(?:="?([\w\-]*)"?)*#', $param, $matched)) {
                                $implParams[$matched[1]] = $matched[2] ?? null;
                            }
                        }




                        $result = $tagInstance->format($input, $implParams);

                        var_dump($input, $result);
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
