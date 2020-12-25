<?php

namespace NGSOFT\Commands\Helpers;

use InvalidArgumentException;
use NGSOFT\Commands\{
    CommandAbstract, Interfaces\Command, Option
};

class Help extends CommandAbstract {

    /** @var array<string,Command> */
    protected $commands = [];

    /**  @return Command[] */
    public function getCommands(): array {
        return $this->commands;
    }

    /**
     * Set Commands to parse arguments
     * @param Command[] $commands
     * @return Help
     * @throws InvalidArgumentException
     */
    public function setCommands(array $commands) {
        foreach ($commands as $command) {
            if (!($command instanceof Command)) throw new InvalidArgumentException('Invalid Command added.');
        }
        $this->commands = $commands;
        return $this;
    }

    public function command(array $args) {
        var_dump($args);
    }

    public function getOptions(): array {

        return [
                    (new Option('command'))
                    ->withDefaultValue('help')
        ];
    }

}
