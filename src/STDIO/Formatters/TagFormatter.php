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
    protected const EMPTY_FORMAT = Format::RESET;

    protected array $formats = [];
    protected array $replacements = [];

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();
        $this->build();
    }

    protected function build(): void
    {
        $formats = &$this->formats;
        $replacements = &$this->replacements;

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
        //$message = str_replace(array_keys($this->replacements), array_values($this->replacements), (string) $message);
        $output = '';

        $offset = 0;

        if (preg_match_all('#<(([a-z](?:[^\\\\<>]*+ | \\\\.)*)|/([a-z][^<>]*+)?)>#ix', $message, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches[0] as $i => $match) {
                [$text, $pos] = $match;

                if (0 != $pos && '\\' == $message[$pos - 1]) {
                    continue;
                }

                $tag = $matches[1][$i][0];
                if ($closing = $tag[0] === '/') {
                    $tag = $matches[3][$i][0] ?? '';
                }


                $formats = [];
                var_dump($tag);
                if ( ! empty($tag)) {
                    $params = [];
                    foreach (preg_split('#;+#', $tag) as $attribute) {
                        [, $key, $val] = preg_exec('#([^=]+)(?:=(.+))?#', $attribute);

                        $key = strtolower($key);
                        if (isset($val)) {
                            foreach (preg_split('#,+#', $val) as $format) {
                                $format = strtolower($format);
                                var_dump([$key, $format]);
                            }





                            continue;
                        }

                        $style = $this->styles[$key] ?? $this->styles->createStyle($tag);
                    }
                } else {
                    $formats[] = self::EMPTY_FORMAT;
                }
            }
        }


        return $output;
    }

}
