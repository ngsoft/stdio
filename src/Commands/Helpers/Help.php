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
                    Option::create('command')
                    ->description('Command to get help screen for.')
                    ->defaultValue('help')
                    ->validateWith(fn($val) => preg_match(Command::VALID_COMMAND_NAME_REGEX, $val) > 0),
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
                ->write(sprintf('  %s ', basename($argv[0])))
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

        //  if ($command === $this) return $this->renderCommandList();


        $io = STDIO::create();

        $options = $command->getOptions();
        $description = $command->getDescription();
        $name = $command->getName();

        if (!empty($description)) $io->linebreak()->writeln($description)->linebreak();
        $io
                ->yellow("Usage:")
                ->linebreak();
        if (!empty($name)) $io->green("  $name ");
        else $io->write('  command ');
        $io->write('[options] [arguments]')->linebreak(2);



        $opts = [];
        $args = [];
        $desc = [];

        $maxlen = 0;

        /** @var Option $option */
        foreach ($options as $option) {

            $optName = $option->getName();

            if ($option->getType() == Option::TYPE_ANONYMOUS) {
                $args[] = $optName;
                $desc[$optName] = [
                    'left' => sprintf('  [%s]', $option->getName()),
                    'right' => $option->getDescription()
                ];
            } else {
                $opts[] = $optName;
                $params = $option->getParams();
                $list = [];
                if (!empty($params['short'])) $list[] = $params['short'];
                if (!empty($params['long'])) $list[] = $params['long'];
                $desc[$optName] = [
                    'left' => sprintf('  %s', implode(', ', $list)),
                    'right' => $option->getDescription()
                ];
            }

            $len = mb_strlen($desc[$optName]['left']);
            if ($len > $maxlen) $maxlen = $len + 4;
        }

        if (count($opts) > 0) {
            $io
                    ->yellow("Options:")
                    ->linebreak();
            foreach ($opts as $optName) {
                $len = mb_strlen($desc[$optName]['left']);
                $repeats = $maxlen - $len;
                $io->green($desc[$optName]['left']);
                if ($repeats > 0) $io->space($repeats);
                $io->writeln($desc[$optName]['right']);
            }
            $io->linebreak();
        }
        if (count($args) > 0) {
            $io
                    ->yellow("Arguments:")
                    ->linebreak();

            foreach ($args as $optName) {
                $len = mb_strlen($desc[$optName]['left']);
                $repeats = $maxlen - $len;
                $io
                        ->green($desc[$optName]['left'])
                        ->space($repeats)
                        ->writeln($desc[$optName]['right']);
            }
            $io->linebreak();
        }
        if (get_class($command) == get_class($this)) {
            $io
                    ->yellow("Available Commands:")
                    ->linebreak();
            $maxlen = 0;
            foreach (array_keys($this->commands) as $name) {
                $len = mb_strlen($name);
                if ($len > $maxlen) $maxlen = $len + 6;
            }
            /** @var Command $command */
            foreach ($this->commands as $name => $command) {
                $len = mb_strlen($name) + 2;
                $repeats = $maxlen - $len;
                $io->green("  $name")
                        ->space($repeats)
                        ->writeln($command->getDescription());
            }
            $io->linebreak();
        }
        $io->out();
    }

}
