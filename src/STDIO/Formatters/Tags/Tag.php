<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use BackedEnum;
use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Enums\Format, Styles\Style, Styles\Styles
};
use function class_basename;

class Tag
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];

    public readonly string $name;

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->name = strtolower(class_basename(static::class));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFormat(array $attributes): string
    {
        return '';
    }

    public function getStyle(array $attributes): Style
    {
        static $availableFormats = [];

        if (empty($availableFormats)) {
            /** @var BackedEnum $enum */
            /** @var Color $format */
            foreach (self::FORMATS_ENUMS as $enum) {
                foreach ($enum::cases() as $format) {
                    $prop = $format->getTagAttribute();
                    $availableFormats[$prop] ??= [];
                    $availableFormats[$prop] [strtolower($format->getName())] = $format;
                }
            }
        }


        $label = '';

        $formats = [];

        foreach ($attributes as $key => $val) {

            if ( ! empty($label)) {
                $label .= ';';
            }
            $label .= $key;

            if (empty($val)) {
                if (isset($this->styles[$key])) {
                    $formats = array_merge($formats, $this->styles[$key]->getStyles());
                }
                continue;
            }
            $label .= sprintf('=%s', implode(',', $val));
            foreach ($val as $format) {

                if (isset($availableFormats[$key][$format])) {
                    $formats[] = $availableFormats[$key][$format];
                }
            }
        }
        return (new Style($label))->setStyles(...$formats);
    }

}
