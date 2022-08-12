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

        var_dump(spl_object_id($this));
    }

    public function format(string|Stringable $message): string
    {
        return (string) $message;
    }

}
