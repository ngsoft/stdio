<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\{
    STDIO, STDIO\Interfaces\Output, STDIO\Interfaces\Renderer, STDIO\Utils\Progress\ProgressElement
};

class Progress implements Renderer {

    /** @var STDIO */
    protected $stdio;

    /** @var ProgressElement[] */
    protected $elements = [];

    /** @var int */
    protected $total;

    public function __construct(
            int $total = 100,
            STDIO $stdio = null
    ) {
        $this->total = $total;
        $this->stdio = $stdio ?? new STDIO();
    }

    protected function build(): string {

        return '';
    }

    public function render(Output $output) {
        $output->write($this->build());
    }

}
