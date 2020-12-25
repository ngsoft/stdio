<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Interfaces\Command, STDIO
};
use RuntimeException;

abstract class CommandAbstract implements Command {

    /** @var STDIO */
    protected $io;

    public function __construct() {
        $this->io = STDIO::create();
    }

    public function parseArguments(array $args): array {

        $result = [];
        $options = $this->getOptions();
        $requested = [];
        $parser = [];
        $annon = [];


        /** @var Option $opt */
        foreach ($options as $opt) {
            $def = $opt->getDefaultValue();
            if ($def !== null) {
                $requested[] = $opt;
                $result[$opt->getName()] = $def;
            } elseif ($opt->getIsBoolean() === true) {
                $requested[] = $opt;
                $result[$opt->getName()] = $opt->getDefaultValue() === true;
            } elseif ($opt->getRequested() === true) $requested[] = $opt;

            if ($opt->getType() === Option::TYPE_SHORT) $parser[$opt->getShortArgument()] = $opt;
            elseif ($opt->getType() === Option::TYPE_VERBOSE) $parser[$opt->getLongArgument()] = $opt;
            elseif ($opt->getType() === Option::TYPE_NAMED) {
                $parser[$opt->getShortArgument()] = $parser[$opt->getLongArgument()] = $opt;
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

                    if ($option->checkValue($arg)) {
                        $result[$option->getName()] = $arg;
                        continue;
                    } else throw new RuntimeException(sprintf('Invalid value "%s" for cli argument "%s"', $arg, $option->getName()));
                } else throw new RuntimeException(sprintf('Cannot parse cli argument "%s"', $arg));
            }

            if ($option->getIsBoolean() === true) {
                $result[$option->getName()] = $result[$option->getName()] !== true;
                continue;
            }

            $next = $i + 1;

            if (isset($args[$next])) {
                if ($option->checkValue($args[$next])) {
                    $result[$option->getName()] = $args[$next];
                } else throw new RuntimeException(sprintf('Invalid value for cli argument "%s"', $option->getName()));
                $i = $next; //jump one arg
            } else throw new RuntimeException(sprintf('Invalid value for argument %s', $option->getName()));
        }

        foreach ($requested as $opt) {
            if (!array_key_exists($opt->getName(), $result)) {
                throw new RuntimeException(sprintf('Required argument "%s" not defined.', $opt->getName()));
            }
        }

        return $result;
    }

    /**
     * Render Help screen for defined Command
     * @param Command $command
     */
    public function getHelpFor(Command $command) {
        $help = new Helpers\Help();
        $help->addCommand($command);
        $help->renderFor($command);
    }

}
