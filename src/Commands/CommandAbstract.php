<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Helpers\Help, Commands\Interfaces\Command, STDIO
};

abstract class CommandAbstract implements Command {

    /** @var STDIO */
    protected $io;

    public function __construct() {
        $this->io = STDIO::create();
    }

    /**
     * Render Help screen for defined Command
     * @param Command $command
     */
    public function getHelpFor(Command $command) {
        $help = new Help();
        $help->addCommand($command);
        $help->renderFor($command);
    }

    /** @return STDIO */
    public function getSTDIO(): STDIO {
        return $this->io;
    }

}
