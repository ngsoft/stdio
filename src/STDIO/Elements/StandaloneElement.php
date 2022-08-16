<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use NGSOFT\{
    Facades\Terminal, STDIO\Styles\StyleList
};
use function mb_strlen,
             mb_substr;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class StandaloneElement extends Element
{

    protected bool $isStandalone = true;
    protected ?string $rendered = null;

    public function __construct(string $tag = '', ?StyleList $styles = null)
    {
        parent::__construct($tag, $styles);

        $this->rendered = $this->render();
    }

    public static function getPriority(): int
    {
        return 10;
    }

    public static function managesAttributes(array $attributes): bool
    {
        // manages only one of those, will not guess <br;hr;tab> is forbidden
        static $managed = ['br', 'hr', 'tab'];
        return count($managed) - 1 === count(array_diff($managed, array_keys($attributes)));
    }

    public function write(string $contents): void
    {
        // never used
    }

    protected function renderThematicChange(): string
    {

        if (empty($char = $this->getAttribute('char') ?? $this->getAttribute('hr'))) {
            $char = '─';
        }

        $char = $this->getValue($char);

        if ( ! is_int($padding = $this->getAttribute('padding'))) {
            $padding = 4;
        }
        $padding = max(0, min($padding, 16));

        if ($padding % 2 === 1) {
            $padding --;
        }


        $width = $max = Terminal::getWidth() - ($padding * 2);

        if (is_int($this->getAttribute('length'))) {
            $width = $this->getAttribute('length');
        }

        $width = max(16, min($max, $width));

        $pad = '';
        if ($padding > 0) {
            $pad = str_repeat(' ', $padding);
        }

        $len = mb_strlen($char);

        $repeats = (int) ceil($width / $len);

        $sep = mb_substr(str_repeat($char, $repeats), 0, $width);

        $this->text = "\n{$pad}" . $sep . "{$pad}\n";

        return ("\n{$pad}" . $this->getStyle()->format($sep) . "{$pad}\n");
    }

    protected function renderRepeatString(string $str, string $param): string
    {

        $count = $this->getAttribute('count') ?? $this->getAttribute($param);

        if ( ! is_int($count)) {
            $count = 1;
        }

        $count = max(1, $count);
        return $this->text = str_repeat($str, $count);
    }

    protected function render(): string
    {

        if ($this->hasAttribute('hr')) {
            return $this->renderThematicChange();
        } elseif ($this->hasAttribute('tab')) {
            return $this->renderRepeatString("\t", 'tab');
        }

        return $this->renderRepeatString("\n", 'br');
    }

    public function __toString(): string
    {
        return $this->rendered;
    }

}
