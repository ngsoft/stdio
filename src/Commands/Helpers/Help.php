<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    CommandAbstract, Option
};

class Help extends CommandAbstract {

    public function command(array $args) {

    }

    public function getOptions(): array {

        return [
                    (new Option('command'))
                    ->withDefaultValue('help')
        ];
    }

}
