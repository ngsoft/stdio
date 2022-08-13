<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException;
use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Enums\Format, Formatters\Tags\BR, Formatters\Tags\HR, Formatters\Tags\Tab, Formatters\Tags\Tag, Styles\Style, Styles\Styles
};
use Stringable;
use function mb_strlen,
             preg_exec,
             str_ends_with,
             str_starts_with;

class TagFormatter implements Formatter
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];
    protected const BUILTIN_TAGS = [BR::class, HR::class, Tab::class];

    protected array $tags = [];
    protected array $stack = [];

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();

        foreach (self::BUILTIN_TAGS as $class) {
            $this->addTag(new $class($this->styles));
        }
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[$tag->getName()] = $tag;
    }

    protected function getTagsFormat(array $attributes): string
    {

        $str = '';
        foreach ($this->tags as $tag) {
            $str .= $tag->getFormat($attributes);
        }
        return $str;
    }

    protected function getCurrentStyle(): Style
    {
        if (empty($this->stack)) {
            return $this->getEmptyStyle();
        }
        return $this->stack[count($this->stack) - 1];
    }

    protected function getEmptyStyle(): Style
    {
        static $empty;
        return $empty ??= new Style();
    }

    protected function push(Style $style): void
    {
        $this->stack[] = $style;
    }

    protected function pop(?Style $style = null): Style
    {

        if (empty($this->stack)) {
            return $this->getEmptyStyle();
        }

        if ( ! $style) {
            return array_pop($this->stack);
        }

        foreach (array_reverse($this->stack) as $index => $current) {
            if ($current->format('', true) === $style->format('', true)) {
                $this->stack = array_slice($this->stack, 0, $index);
                return $current;
            }
        }
        throw new InvalidArgumentException(sprintf('Incorrect style tag "</%s>" found.', $style));
    }

    protected function applyStyle(string $message, Style $style = null)
    {
        if (is_null($style)) {
            $style = $this->getCurrentStyle();
        }
        return $style->format($message, $this->styles->colors);
    }

    /**
     * Escapes < and > special chars in given text.
     */
    public static function escape(string $message): string
    {
        return static::escapeTrailingBackslash(preg_replace('/([^\\\\]|^)([<>])/', '$1\\\\$2', $message));
    }

    public static function escapeTrailingBackslash(string $message): string
    {
        if (str_ends_with($message, '\\')) {
            $len = mb_strlen($message);
            $message = rtrim($message, '\\');
            $message = str_replace("\0", '', $message);
            $message .= str_repeat("\0", $len - mb_strlen($message));
        }
        return $message;
    }

    /** {@inheritdoc} */
    public function format(string|Stringable $message): string
    {

        $output = '';
        $offset = 0;

        if (preg_match_all('#<(([a-z](?:[^\\\\<>]*+ | \\\\.)*)|/([a-z][^<>]*+)?)>#ix', $message, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches[0] as $i => $match) {
                [$text, $pos] = $match;

                if (0 != $pos && '\\' == $message[$pos - 1]) {
                    continue;
                }

                $output .= $this->applyStyle(substr($message, $offset, $pos - $offset));
                $offset = $pos + strlen($text);

                $tag = $matches[1][$i][0];
                if ($closing = str_starts_with($tag, '/')) {
                    $tag = $matches[3][$i][0] ?? '';
                }

                $style = null;

                if ( ! empty($tag)) {

                    $attributes = [];
                    foreach (preg_split('#;+#', $tag) as $attribute) {
                        [, $key, $val] = preg_exec('#([^=]+)(?:=(.+))?#', $attribute);
                        $key = strtolower(trim($key));
                        $attributes[strtolower(trim($key))] = isset($val) ? array_map(fn($v) => trim($v), preg_split('#,+#', $val)) : [];
                    }

                    if ( ! empty($str = $this->getTagsFormat($attributes))) {
                        $output .= $str;
                        continue;
                    }

                    if ( ! isset($this->styles[$tag])) {
                        $this->styles->addStyle(
                                $style = $this->styles->createStyleFromAttributes($attributes, $tag)
                        );
                    } else { $style = $this->styles[$tag]; }
                }


                if ($closing) {
                    $this->pop($style);
                    continue;
                }


                $style && $this->push($style);
            }
        }

        $output .= $this->applyStyle(substr($message, $offset));

        return strtr($output, [
            "\0" => '\\',
            '\\<' => '<',
            '\\>' => '>',
            "\t" => '    ',
            '\t' => '    ',
            '\s' => ' ',
            '\n' => "\n",
            '\r' => "\r",
        ]);
    }

}
