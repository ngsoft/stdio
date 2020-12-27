<?php

declare(strict_types=1);

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
        $result = '';
        $str = "Hello $name !";
        if ($args['uppercase'] === true) $str = strtoupper($str);
        elseif ($args['lowercase'] === true) $str = strtolower($str);

        for ($i = 0; $i < $args['repeat']; $i++) {
            if (!empty($result)) $result .= "\n";
            $result .= $str;
        }
        return $result;
    }

    public function getDescription(): string {

        return "Hello World";
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
                    BooleanOption::create("uppercase", '-u', '--upper')
                    ->description('Transform to uppercase.'),
                    BooleanOption::create("lowercase", '-l', '--lower')
                    ->description('Transform to lowercase.'),
                    Option::create('repeat', '-r', '--repeat')
                    ->isInt()
                    ->description('Number of repeats')
                    ->defaultValue(1)
        ];
    }

}
