<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Formatters\Tags\DefaultTag, Formatters\Tags\HR, Styles\Style, StyleSheet
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
        $this->setDefaultTag(new DefaultTag($styles));
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



            // $message = str_replace(array_keys($this->replaceTags), array_values($this->replaceTags), $message);

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
                            if (preg_match('#([\w\-]+)(?:="?([\w\-]*)"?)#', $param, $matched)) {
                                if ($tagName === $param) $tagName = '';
                                list(, $key, $value) = $matched;
                                if ($key === 'tagName') continue;
                                $value = explode(',', $value);
                                $value = array_map(fn($v) => trim($v), $value);
                                $implParams[$key] = count($value) === 1 ? $value[0] : $value;
                            }
                        }
                        $implParams['tagName'] = $tagName;
                        $implParams['closing'] = $closing;
                        return $tagInstance->format($input, $implParams);
                    }
                }




                return $input;
            }, $message);

            $result .= $message;
        }

        return $result;
    }

    public function __debugInfo(): array {
        return [
            'tags' => $this->tags,
        ];
    }

}
