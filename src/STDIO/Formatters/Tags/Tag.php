<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Styles\Styles;

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

    public function managesAttributes(array $attributes): bool
    {
        return false;
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

        $formats = [];

        foreach ($attributes as $key => $val) {

            if (empty($val)) {
                if (isset($this->styles[$key])) {
                    $formats = array_merge($formats, $this->styles[$key]->getStyles());
                }
                continue;
            }


            foreach ($val as $format) {
                if (isset($availableFormats[$key][$format])) {
                    $formats[] = $availableFormats[$key][$format];
                }
            }
        }
        return (new \NGSOFT\STDIO\Styles\Style(''))->setStyles(...$formats);
    }

}
