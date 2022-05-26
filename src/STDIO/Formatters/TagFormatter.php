<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Formatters\Tags\BR, Formatters\Tags\DefaultTag, Formatters\Tags\HR, Formatters\Tags\Tab, StyleSheet
};
use Stringable,
    ValueError;

class TagFormatter implements FormatterInterface {

    protected const BUILTIN = [
        HR::class,
        BR::class,
        Tab::class,
    ];

    /** @var array<string,string> */
    protected array $replacements = [
        "\t" => "  ",
        "\s" => " ",
        "&gt;" => '>',
        "&lt;" => '<',
    ];

    /** @var array<string,string> */
    protected array $replaceTags = [];
    protected array $tags = [];
    protected Tag $defaultTag;
    protected StyleSheet $styles;

    public function __construct(StyleSheet $styles = null) {
        $this->styles = $styles ?? new StyleSheet();
        $this->setDefaultTag(new DefaultTag($this->styles));
        foreach (self::BUILTIN as $className) {
            $this->addTag(new $className($this->styles));
        }
        $this->build();
    }

    protected function build(): void {

        $result = [
            '</>' => $this->styles['reset']->getPrefix(),
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

            // defined tags <br> <hr> or custom <bg=black;fg="white";options=bold,italic>
            // <tagname;param1=value1;title="my long title...";param2=value 2,value3>


            $message = preg_replace_callback('#<(\/)*([^>]*)>#', function ($matches) {
                list($input, $closing, $contents) = $matches;
                $closing = !empty($closing);
                // </> or <>
                if (!empty($contents)) {
                    $params = preg_split('#;+#', $contents);
                    $tagName = strtolower($params[0]);
                    /** @var Tag $tagInstance */
                    $tagInstance = $this->tags[$tagName] ?? $this->defaultTag;

                    $implParams = [];

                    foreach ($params as $param) {
                        if (preg_match('#([\w\-]+)=(.*)#', $param, $matched)) {
                            // <key=value>
                            if ($tagName === $param) $tagName = '';
                            list(, $key, $value) = $matched;
                            $value = trim($value, '"');
                            $value = explode(',', $value);
                            $value = array_map(fn($v) => trim($v), $value);
                            $implParams[$key] = $value;
                        }
                    }
                    $implParams['tagName'] = $tagName;
                    $implParams['closing'] = $closing;
                    return $tagInstance->format($input, $implParams);
                }

                return $input;
            }, $message);
            // prebuild tags
            $message = str_replace(array_keys($this->replaceTags), array_values($this->replaceTags), $message);
            //unknown tags
            $message = strip_tags($message);
            // \t \s and other things
            $message = str_replace(array_keys($this->replacements), array_values($this->replacements), $message);

            $result .= $message;
        }

        return $result;
    }

    public function __debugInfo(): array {
        return [];
    }

}
