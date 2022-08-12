<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use Stringable;

class TagFormatter implements Formatter
{

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

        var_dump($this);
    }

    public function format(string|Stringable $message): string
    {
        return (string) $message;
    }

}
