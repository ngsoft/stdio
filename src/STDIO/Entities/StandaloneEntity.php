<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use Stringable;
use function mb_strlen,
             mb_substr;

class StandaloneEntity extends Entity
{

    protected bool $standalone = true;

    public static function getPriority(): int
    {
        return 10;
    }

    public static function matches(array $attributes): bool
    {
        static $managed = ['br', 'hr', 'tab'];
        return count($managed) - 1 === count(array_diff($managed, array_keys($attributes)));
    }

    protected function renderThematicChange(): string
    {


        if (empty($char = $this->getValue($this->getAttribute('char') ?? $this->getAttribute('hr')))) {
            $char = '─';
        }


        $padding = $this->getInt($this->getAttribute('padding'), 4);

        $padding = max(0, min($padding, 16));

        if ($padding % 2 === 1) {
            $padding --;
        }


        $width = $max = $this->terminal->getWidth() - ($padding * 2);

        $width = $this->getInt($this->getAttribute('length'), $width);

        $width = max(16, min($max, $width));

        $pad = '';
        if ($padding > 0) {
            $pad = str_repeat(' ', $padding);
        }

        $len = mb_strlen($char);

        $repeats = (int) ceil($width / $len);

        $sep = mb_substr(str_repeat($char, $repeats), 0, $width);

        return "\n{$pad}" . $this->getStyle()->format($sep) . "{$pad}\n";
    }

    protected function renderRepeatString(string $str, string $param): string
    {

        $count = $this->getAttribute('count') ?? $this->getAttribute($param);
        $count = $this->getInt($count, 1);
        $count = max(1, $count);
        return str_repeat($str, $count);
    }

    /**
     * @phan-suppress PhanUnusedPublicMethodParameter
     */
    public function format(string|Stringable $message): string
    {
        if ( ! static::matches($this->attributes)) {
            return '';
        }

        if ($this->hasAttribute('hr')) {
            return $this->renderThematicChange();
        } elseif ($this->hasAttribute('tab')) {
            return $this->renderRepeatString("\t", 'tab');
        }

        return $this->renderRepeatString("\n", 'br');
    }

}
