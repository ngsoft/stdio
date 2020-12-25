<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    CommandAbstract, Option
};

class Hello extends CommandAbstract {

    public function command(array $args) {


        var_dump($args);
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
                    ->withDefaultValue("World"),
                    Option::create("uppercase")
                    ->withIsBoolean()
                    ->withShortArgument('-u'),
                    Option::create("lowercase")
                    ->withIsBoolean()
                    ->withShortArgument('-l'),
        ];
    }

}
