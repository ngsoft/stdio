<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    CommandAbstract, Option
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
                    Option::create("name", 'Name to display')
                    ->withDefaultValue("World"),
                    Option::create("uppercase", 'Transform to uppercase.')
                    ->withIsBoolean()
                    ->withShortArgument('-u'),
                    Option::create("lowercase", 'Transform to lowercase.')
                    ->withIsBoolean()
                    ->withShortArgument('-l'),
        ];
    }

}
