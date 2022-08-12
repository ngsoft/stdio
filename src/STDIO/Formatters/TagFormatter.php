<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use BackedEnum;
use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Enums\Format, Formatters\Tags\Tag, Styles\Style, Styles\Styles
};
use Stringable;
use function preg_exec,
             str_starts_with;

class TagFormatter implements Formatter
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];
    protected const BUILTIN_TAGS = [];

    protected array $replacements = [];
    protected array $tags = [];
    protected Tag $tag;

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();

        $this->tag = new Tag($this->styles);

        foreach (self::BUILTIN_TAGS as $class) {
            $this->addTag(new $class($this->styles));
        }

        $this->build();
    }

    public function addTag(Tag $tag): void
    {
        $class = get_class($tag);
        if ($class === Tag::class) {
            return;
        }
        $this->tags[$class] = $tag;
    }

    protected function build(): void
    {
        $formats = &$this->formats;

        /** @var Style $style */
        foreach ($this->styles as $label => $style) {
            $this->replacements[sprintf('<%s>', $label)] = $style->getPrefix();
            $this->replacements[sprintf('</%s>', $label)] = $style->getSuffix();
        }
    }

    protected function getTagsFormat(array $attributes): string
    {
        //special tags
        return '';
    }

    public function format(string|Stringable $message): string
    {

        // builtin styles
        $message = str_replace(array_keys($this->replacements), array_values($this->replacements), (string) $message);

        $output = '';

        $offset = 0;

        if (preg_match_all('#<(([a-z](?:[^\\\\<>]*+ | \\\\.)*)|/([a-z][^<>]*+)?)>#ix', $message, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches[0] as $i => $match) {
                [$text, $pos] = $match;

                if (0 != $pos && '\\' == $message[$pos - 1]) {
                    continue;
                }

                $output .= substr($message, $offset, $pos - $offset);
                $offset = $pos + strlen($text);
                // text to be added to the output
                $str = '';
                $tag = $matches[1][$i][0];

                if ($closing = str_starts_with($tag, '/')) {
                    $tag = $matches[3][$i][0] ?? '';
                }

                if ( ! empty($tag)) {

                    $attributes = [];
                    foreach (preg_split('#;+#', $tag) as $attribute) {
                        [, $key, $val] = preg_exec('#([^=]+)(?:=+(.+))?#', $attribute);
                        $key = strtolower(trim($key));
                        $attributes[strtolower(trim($key))] = isset($val) ? array_map(fn($v) => strtolower(trim($v)), preg_split('#,+#', $val)) : [];
                    }



                    if ( ! empty($str = $this->getTagsFormat($attributes))) {
                        $output .= $str;
                        continue;
                    }

                    if ( ! isset($this->styles[$tag])) {

                        $this->styles->addStyle($style = $this->tag->getStyle($attributes));
                    } else { $style = $this->styles[$tag]; }
                } else {
                    $style = $this->styles['reset'];
                }
                var_dump($style);

                if ($this->styles->colors) {
                    $str = $closing ? $style->getSuffix() : $style->getPrefix();
                }

                $output .= $str;
            }
        }


        $output .= substr($message, $offset);

        return $output;
    }

}
