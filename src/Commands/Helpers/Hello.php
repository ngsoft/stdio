<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    BooleanOption, CommandAbstract, Option
};

class Hello extends CommandAbstract {

    public function command(array $args) {

        if ($args['help'] === true) {
            $help = new Help();
            return $help->renderFor($this);
        }

        $name = $args['name'];
        $str = "Hello $name !";

        if ($args['uppercase'] === true) $str = strtoupper($str);
        elseif ($args['lowercase'] === true) $str = strtolower($str);


        return $str;
    }

    public function getDescription(): string {

        return "A simple hello world";
    }

    public function getName(): string {
        return "hello";
    }

    public function getOptions(): array {

        return [
                    Option::create("name")
                    ->description('Name to display')
                    ->defaultValue("World"),
                    BooleanOption::create('help', '-h', '--help')
                    ->description('Display this help message'),
                    BooleanOption::create("uppercase", '-u')
                    ->description('Transform to uppercase.'),
                    BooleanOption::create("lowercase", '-l')
                    ->description('Transform to lowercase.'),
        ];
    }

}
