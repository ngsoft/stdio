<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use NGSOFT\Facades\Terminal;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class StandaloneElement extends Element
{

    protected bool $isStandalone = true;

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

        $char = $this->getAttribute('char') ?? $this->getAttribute('hr');
        if (empty($char)) {
            $char = '─';
        }

        if ( ! is_int($padding = $this->getAttribute('padding'))) {
            $padding = 4;
        }
        $padding = max(0, min($padding, 16));

        if ($padding % 2 === 1) {
            $padding --;
        }

        $width = Terminal::getWidth() - ($padding * 2);
        $pad = '';
        if ($padding > 0) {
            $pad = str_repeat(' ', $padding);
        }

        $length = mb_strlen($char);

        $repeats = (int) ceil($width / $length);
        $line = $this->getStyle()->format(mb_substr(str_repeat($char, $repeats), 0, $length));
        return $this->styles['reset']->format(sprintf("\n%s\n", $line));
    }

    protected function renderRepeatString(string $str, string $param): string
    {

        $count = $this->getAttribute('count') ?? $this->getAttribute($param);

        if ( ! is_int($count)) {
            $count = 1;
        }

        $count = max(1, $count);
        return str_repeat($str, $count);
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
        return $this->render();
    }

}
