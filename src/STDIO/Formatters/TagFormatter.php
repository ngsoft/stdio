<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\{
    Formatters\Tags\BR, Formatters\Tags\HR, Formatters\Tags\Space, Formatters\Tags\Tab, Interfaces\Formatter, Interfaces\Tag, Styles
};

class TagFormatter implements Formatter {

    protected const BUILTIN_TAGS = [
        Space::class,
        Tab::class,
        BR::class,
        HR::class,
    ];

    /** @var Styles */
    protected $styles;

    /** @var array<string,string> */
    protected $tags = [];

    /** @var array<string,Tag> */
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
        foreach (self::BUILTIN_TAGS as $className) {
            $this->addTag(new $className());
        }
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

    ////////////////////////////   Tags   ////////////////////////////

    /**
     * Adds a Format Tag
     * @param Tag $tag
     * @return static
     */
    public function addTag(Tag $tag) {
        $this->formatTags[$tag->getName()] = $tag;
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

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
     * Format using Tag
     */
    private function formatExtraTags(string $message): string {


        $message = str_replace(['<\\', '\\>'], ['</', '/>'], $message);

        $message = preg_replace_callback('/[<](?P<closing>[\/])*(?P<tag>\w+)(?:\s+(?P<extra>.*?))?(?:[\/])*[>]/', function ($matches) {
            $input = $matches[0];

            $closing = !empty($matches['closing']);

            $params = [];

            $extra = $matches['extra'] ?? null;
            $tag = strtolower($matches['tag']);

            if (is_string($extra)) {
                if (preg_match_all('/(\w+)\=[\'\"]*([\w\-]+)[\'\"]*/', $extra, $out) !== false) {
                    list(, $keys, $values) = $out;
                    $params = array_combine($keys, $values);
                }
            }
            $params['closing'] = $closing;

            if (isset($this->formatTags[$tag])) return $this->formatTags[$tag]->format($params);
            return $input;
        }, $message);

        return $message;
    }

}
