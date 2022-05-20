<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Formatters\TagFormatter, Inputs\StreamInput, Interfaces\Ansi, Interfaces\Buffer, Interfaces\Colors, Interfaces\Formats, Interfaces\Formatter, Interfaces\Input,
    Interfaces\Output, Outputs\ErrorStreamOutput, Outputs\OutputBuffer, Outputs\StreamOutput, Styles, Terminal, Utils\Cursor, Utils\Progress, Utils\Rect
};

class STDIO {

    public const VERSION = '3.0';

}
