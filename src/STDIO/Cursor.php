<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\{
    Inputs\Input, Outputs\Output, Utils\Utils
};

class Cursor {

    /** @var Terminal */
    private $terminal;

    /** @var Input */
    private $input;

    /** @var Output */
    private $output;

    public function __construct(
            Output $output = null,
            Input $input = null
    ) {
        $this->terminal = Terminal::create();
        $this->output = $output ?? new Output();
        $this->input = $input ?? new Input();
    }

    public function getCurrentPosition() {

        $ttySupport = $this->terminal->tty;

        if ($mode = Utils::executeProcess('stty -g')) {
            Utils::executeProcess('stty -icanon -echo');

            Utils::executeProcess(sprintf('stty %s', $mode));
        }



        var_dump($mode);
    }

}
