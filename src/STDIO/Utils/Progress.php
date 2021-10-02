<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\{
    STDIO, STDIO\Interfaces\Renderer, STDIO\Utils\Progress\ProgressElement
};

class Progress implements Renderer {

    /** @var STDIO */
    protected $stdio;

    /** @var ProgressElement[] */
    protected $elements = [];

    /** @var int */
    protected $total = 100;

    public function __construct(
            STDIO $stdio,
            int $total
    ) {

    }

}
