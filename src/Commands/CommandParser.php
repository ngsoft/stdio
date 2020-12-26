<?php

namespace NGSOFT\Commands;

use NGSOFT\Commands\Interfaces\Parser,
    RuntimeException;

class CommandParser implements Parser {

    public function parseArguments(array $args, array $options): array {
        $result = [];

        $required = [];
        $parser = [];
        $annon = [];

        /** @var Option $opt */
        foreach ($options as $opt) {
            $params = $opt->getParams();
            $def = $params['defaultValue'];

            if ($def !== null) {
                $required[] = $opt;
                $result[$opt->getName()] = $def;
            } elseif ($opt->getValueType() == Option::VALUE_TYPE_BOOLEAN) {
                $required[] = $opt;
                $result[$opt->getName()] = $def === true;
            } elseif ($params['required'] === true) $required[] = $opt;

            if ($opt->getType() === Option::TYPE_SHORT) $parser[$params['short']] = $opt;
            elseif ($opt->getType() === Option::TYPE_VERBOSE) $parser[$params['long']] = $opt;
            elseif ($opt->getType() === Option::TYPE_NAMED) {
                $parser[$params['short']] = $parser[$params['long']] = $opt;
            } else $annon[] = $opt;
        }

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args[$i];
            $option = null;
            foreach ($parser as $str => $opt) {
                if ($arg === $str) {
                    $option = $opt;
                    break;
                }
            }
            if ($option === null) {
                if (count($annon) > 0) {
                    $option = array_shift($annon);
                    if ($option->validateArgument($arg)) {
                        $result[$option->getName()] = $option->transformArgument($arg);
                        continue;
                    } else throw new RuntimeException(sprintf('Invalid value "%s" for cli argument "%s"', $arg, $option->getName()));
                } else throw new RuntimeException(sprintf('Cannot parse cli argument "%s"', $arg));
            }

            if ($option->getValueType() === Option::VALUE_TYPE_BOOLEAN) {
                $result[$option->getName()] = $result[$option->getName()] !== true;
                continue;
            }

            $next = $i + 1;

            if (isset($args[$next])) {
                if ($option->validateArgument($args[$next])) {
                    $result[$option->getName()] = $option->transformArgument($args[$next]);
                } else throw new RuntimeException(sprintf('Invalid value for cli argument "%s"', $option->getName()));
                $i = $next; //jump one arg
            } else throw new RuntimeException(sprintf('Invalid value for argument %s', $option->getName()));
        }

        foreach ($required as $opt) {
            if (!array_key_exists($opt->getName(), $result)) {
                throw new RuntimeException(sprintf('Required argument "%s" not defined.', $opt->getName()));
            }
        }

        return $result;
    }

}
