<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use BackedEnum;
use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Enums\Format, Styles\Style, Styles\Styles
};
use Stringable;

class TagFormatter implements Formatter
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];

    protected array $formats = [];
    protected array $replacements = [];
    protected array $tags = [];

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();
        $this->build();
    }

    protected function build(): void
    {
        $formats = &$this->formats;

        /** @var Style $style */
        foreach ($this->styles as $label => $style) {
            $this->replacements[sprintf('<%s>', $label)] = $style->getPrefix();
            $this->replacements[sprintf('</%s>', $label)] = $style->getSuffix();
        }

        /** @var BackedEnum $enum */
        /** @var Color $format */
        foreach (self::FORMATS_ENUMS as $enum) {
            foreach ($enum::cases() as $format) {
                $prop = $format->getTagAttribute();
                $formats[$prop] ??= [];
                $formats[$prop] [strtolower($format->getName())] = $format;
            }
        }
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

                    if ( ! isset($this->styles[$tag])) {

                        $formats = [];

                        foreach ($attributes as $key => $val) {

                            if (empty($val)) {
                                if (isset($this->styles[$key])) {
                                    $formats = array_merge($formats, $this->styles[$key]->getStyles());
                                }
                                continue;
                            }


                            foreach ($val as $format) {
                                if (isset($this->formats[$key][$format])) {
                                    $formats[] = $this->formats[$key][$format];
                                }
                            }
                        }

                        $style = $this->styles->createStyle($tag, ...$formats);
                        if (empty($str)) {
                            $this->styles->addStyle($style);
                        }
                    } else { $style = $this->styles[$tag]; }
                } else {
                    $style = $this->styles['reset'];
                }


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
