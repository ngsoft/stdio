<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException;
use NGSOFT\STDIO\Interfaces\{
    Ansi, Colors, Formats
};

final class Style {

    /** @var string|null */
    private $name = 'style';

    /** @var int|null */
    private $color;

    /** @var int|null */
    private $background;

    /** @var int[] */
    private $formats = [];

    /** @var string|null */
    private $prefix;

    /** @var string|null */
    private $suffix;

    /** @var bool */
    private $supported;

    public function __construct(bool $supported) {
        $this->supported = $supported;
    }

    ////////////////////////////   Configurators   ////////////////////////////

    /**
     * Returns a clone with declared name
     * @param string $name
     * @return Style
     */
    public function withName(string $name): Style {
        return $this->getClone()->setName($name);
    }

    /**
     * Returns a clone with declared color
     * @param int $color
     * @return Style
     */
    public function withColor(int $color): Style {
        return $this->getClone()->setColor($color);
    }

    /**
     * Returns a clone without color
     * @return Style
     */
    public function withoutColor(): Style {
        $clone = $this->getClone();
        $clone->color = null;
        return $clone;
    }

    /**
     * Returns a clone with declared background
     * @param int $background
     * @return Style
     */
    public function withBackground(int $background): Style {
        return $this->getClone()->setBackground($background);
    }

    /**
     * Returns a clone without background
     * @return Style
     */
    public function withoutBackground(): Style {
        $clone = $this->getClone();
        $clone->background = null;
        return $clone;
    }

    /**
     * Returns a clone with declared formats
     * @param array $formats
     * @return Style
     */
    public function withFormats(array $formats): Style {
        return $this->getClone()->setFormats($formats);
    }

    /**
     *  Returns a clone without formats
     * @return Style
     */
    public function withoutFormats(): Style {
        $clone = $this->getClone();
        $clone->formats = [];
        return $clone;
    }

    /**
     * Returns a clone with single format added
     * @param int $format
     * @return Style
     * @throws InvalidArgumentException
     */
    public function withAddedFormat(int $format): Style {

        if (!in_array($format, Formats::FORMAT_VALID)) {
            throw new InvalidArgumentException("Invalid Format code $format");
        }
        $clone = $this->getClone();
        $clone->formats [] = $format;
        return $clone;
    }

    /**
     * Returns a clone with multiple formats added
     * @param array $formats
     * @return Style
     */
    public function withAddedFormats(array $formats): Style {
        $clone = $this->getClone();
        foreach ($formats as $format) {
            $clone = $clone->withAddedFormat($format);
        }
        return $clone;
    }

    /** @return Style */
    private function getClone(): Style {
        return clone $this;
    }

    ////////////////////////////   GETTERS/SETTERS   ////////////////////////////

    /**
     * Get Style Name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Set Style Name
     * @param string $name
     * @return Style
     */
    public function setName(string $name): Style {
        $this->prefix = $this->suffix = null;
        $this->name = $name;
        return $this;
    }

    /**
     * Get Current Set Color
     * @return int|null
     */
    public function getColor(): ?int {
        return $this->color;
    }

    /**
     * Get Current Set Background
     * @return int|null
     */
    public function getBackground(): ?int {
        return $this->background;
    }

    /**
     * Get Current Formats
     * @return array
     */
    public function getFormats(): array {
        return $this->formats;
    }

    /**
     *  Set the Color
     * @param int $color
     * @return Style
     * @throws InvalidArgumentException
     */
    public function setColor(int $color): Style {
        if (!in_array($color, Colors::COLOR_VALID)) {
            throw new InvalidArgumentException("Invalid Color code $color");
        }
        $this->prefix = $this->suffix = null;

        $this->color = $color;
        return $this;
    }

    /**
     * Set the Background
     * @param int $background
     * @return Style
     * @throws InvalidArgumentException
     */
    public function setBackground(int $background): Style {
        if (!in_array($background, Colors::COLOR_VALID)) {
            throw new InvalidArgumentException("Invalid Color code $background");
        }
        $this->prefix = $this->suffix = null;
        $this->background = $background;
        return $this;
    }

    /**
     * Set the Formats
     * @param int[] $formats
     * @return Style
     */
    public function setFormats(array $formats): Style {
        foreach ($formats as $format) {
            if (!in_array($format, Formats::FORMAT_VALID)) {
                throw new InvalidArgumentException("Invalid Format code $format");
            }
        }
        $this->prefix = $this->suffix = null;
        $this->formats = $formats;
        return $this;
    }

    ////////////////////////////   Formatters   ////////////////////////////

    /**
     * Compile the prefix and suffix
     * @internal
     * @return Style
     */
    public function compile(): Style {
        $this->prefix = $this->suffix = null;
        $this->getPrefix();
        $this->getSuffix();
        return $this;
    }

    /**
     * Get Prefix as string
     * @return string
     */
    public function getPrefix(): string {
        //compile prefix
        if ($this->prefix === null && $this->supported) {
            $prefix = '';
            $params = [];
            if (count($this->formats)) $params = $this->formats;
            if (is_int($this->color)) $params[] = $this->color;
            if (is_int($this->background)) $params[] = $this->background + Colors::BACKGROUND_COLOR_MODIFIER;
            if (count($params) > 0) {
                $prefix = sprintf(Ansi::ESCAPE . '%s' . Ansi::STYLE_SUFFIX, implode(';', $params));
            }
            $this->prefix = $prefix;
        }
        return $this->prefix ?? '';
    }

    /**
     * Get Suffix as string
     * @return string
     */
    public function getSuffix(): string {
        if ($this->suffix === null && $this->supported) {
            $suffix = '';
            $params = [];
            if (count($this->formats)) {
                $params = array_map(function ($val) {
                    return Formats::FORMAT_UNSET[$val];
                }, $this->formats);
            }

            if (is_int($this->color)) $params[] = Colors::COLOR_UNSET[$this->color];
            if (is_int($this->background)) $params[] = Colors::COLOR_UNSET[$this->background] + Colors::BACKGROUND_COLOR_MODIFIER;
            if (count($params) > 0) {
                $suffix = sprintf(Ansi::ESCAPE . '%s' . Ansi::STYLE_SUFFIX, implode(';', $params));
            }

            $this->suffix = $suffix;
        }
        return $this->suffix ?? '';
    }

    /**
     * Format the string using current style
     * @param string $message
     * @return string
     */
    public function format(string $message): string {
        return sprintf("%s%s%s", $this->getPrefix(), $message, $this->getSuffix());
    }

}
