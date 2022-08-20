<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use NGSOFT\Facades\Terminal;
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

    protected function renderThematicChange(): void
    {

        if (empty($char = $this->getAttribute('char') ?? $this->getAttribute('hr'))) {
            $char = '─';
        }

        $char = $this->getValue($char);

        $padding = $this->getInt($this->getAttribute('padding'), 4);

        $padding = max(0, min($padding, 16));

        if ($padding % 2 === 1) {
            $padding --;
        }


        $width = $max = Terminal::getWidth() - ($padding * 2);

        $width = $this->getInt($this->getAttribute('length'), $width);

        $width = max(16, min($max, $width));

        $pad = '';
        if ($padding > 0) {
            $pad = str_repeat(' ', $padding);
        }

        $len = mb_strlen($char);

        $repeats = (int) ceil($width / $len);

        $sep = mb_substr(str_repeat($char, $repeats), 0, $width);

        $this->children = [
                    Message::create()
                    ->setText("\n{$pad}" . $sep . "{$pad}\n")
                    ->setFormatted("\n{$pad}" . $this->getStyle()->format($sep) . "{$pad}\n")
        ];
    }

    protected function renderRepeatString(string $str, string $param): void
    {

        $count = $this->getAttribute('count') ?? $this->getAttribute($param);
        $count = $this->getInt($count, 1);
        $count = max(1, $count);
        $result = str_repeat($str, $count);

        $this->children = [Message::create($result)];
    }

    protected function build(): string
    {
        if ( ! $this->formatted) {
            if ($this->hasAttribute('hr')) {
                $this->renderThematicChange();
            } elseif ($this->hasAttribute('tab')) {
                $this->renderRepeatString("\t", 'tab');
            } elseif ($this->hasAttribute('br')) {
                $this->renderRepeatString("\n", 'br');
            }
        }
        return parent::build();
    }

}
