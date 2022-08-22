<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use NGSOFT\DataStructure\Text;

/**
 * A Message
 */
class Message extends Text
{

    public function getText(): string
    {
        return $this->text;
    }

}
