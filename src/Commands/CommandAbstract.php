<?php

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Interfaces\Command, STDIO
};

abstract class CommandAbstract implements Command {

    /** @var STDIO */
    protected $io;

    public function __construct() {
        $this->io = STDIO::create();
    }

    public function parseArguments(array $args): array {

        $result = [];
        $options = $this->getOptions();




        $length = count($args);

        for ($i = 0; $i < count($args); $i++) {

            echo "$i\n";
            if ($i == 5) $i++;
        }






        return $result;
    }

}
