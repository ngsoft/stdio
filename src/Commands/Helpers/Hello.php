<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    BooleanOption, CommandAbstract, Option
};

class Hello extends CommandAbstract {

    public function command(array $args) {

        $name = $args['name'];
        $str = "Hello $name !";

        if ($args['uppercase'] === true) $str = strtoupper($str);
        elseif ($args['lowercase'] === true) $str = strtolower($str);


        return $str;
    }

    public function getDescription(): string {

        return "A simple Hello World";
    }

    public function getName(): string {
        return "hello";
    }

    public function getOptions(): array {

        return [
                    Option::create("name")
                    ->description('Name to display')
                    ->defaultValue("World"),
                    BooleanOption::create("uppercase", '-u')
                    ->description('Transform to uppercase.'),
                    BooleanOption::create("lowercase", '-l')
                    ->description('Transform to lowercase.'),
        ];
    }

}
