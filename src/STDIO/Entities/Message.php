<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use NGSOFT\DataStructure\Text;

/**
 * A Message
 */
class Message extends Text
{

    public static function create(string $text = ''): static
    {
        return new static($text);
    }

    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

}
