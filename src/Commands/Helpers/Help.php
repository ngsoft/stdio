<?php

namespace NGSOFT\Commands\Helpers;

use NGSOFT\{
    Commands\CommandAbstract, Commands\Interfaces\Command, Commands\Option, STDIO
};
use RuntimeException;
use function mb_strlen;

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
                    Option::create('command', 'Command to get help screen for.')
                    ->withDefaultValue('help')
                    ->withMustBe(fn($val) => preg_match(Command::VALID_COMMAND_NAME_REGEX, $val) > 0),
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

    protected function renderCommandList() {

        global $argv;
        $io = STDIO::create();

        $io
                ->yellow("Usage:")
                ->linebreak()
                ->write(sprintf('  %s ', $argv[0]))
                ->green('help [command]')
                ->linebreak();

        $io
                ->linebreak()
                ->yellow("Available Commands:")
                ->linebreak();

        $maxlen = 0;
        foreach (array_keys($this->commands) as $name) {
            if (mb_strlen($name) > $maxlen) {
                $maxlen = mb_strlen($name);
            }
        }
        $maxlen += 4;
        /** @var Command $command */
        foreach ($this->commands as $name => $command) {
            $len = mb_strlen($name) + 2;
            $repeats = $maxlen - $len;
            $io
                    ->green(sprintf('  %s', $name))
                    ->space($repeats)
                    ->write($command->getDescription())
                    ->linebreak();
        }

        $io->out();
    }

    public function renderFor(Command $command) {

        if ($command === $this) return $this->renderCommandList();
    }

}
