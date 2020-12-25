<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\Commands\{
    CommandAbstract, Interfaces\Command, Option
};
use RuntimeException;

class Help extends CommandAbstract {

    /** @var array<string,Command> */
    protected $commands = [];

    /**  @return array<string,Command> */
    public function getCommands(): array {
        return $this->commands;
    }

    /**
     * Add a Command to the stack
     * @param Command $command
     * @param string|null $name
     * @return Help
     */
    public function addCommand(Command $command, ?string $name = null): Help {
        if ($name === null) $name = $command->getName();
        $this->commands[$name] = $command;
        return $this;
    }

    /** {@inheritdoc} */
    public function getOptions(): array {

        return [
                    (new Option('command'))
                    ->withDefaultValue('help')
                    ->withMustBe(fn($val) => preg_match(Command::VALID_COMMAND_NAME_REGEX, $val) > 0)
        ];
    }

    /** {@inheritdoc} */
    public function getDescription(): string {
        return "This help screen";
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return "help";
    }

    public function command(array $args) {

        $command = $args['command'];

        if (
                isset($this->commands[$command])
                and ($this->commands[$command] instanceof Command)
        ) {
            return $this->renderFor($this->commands[$command]);
        }
        throw new RuntimeException(sprintf('Command "%s" not found.', $command));
    }

    public function renderFor(Command $command) {

    }

}
