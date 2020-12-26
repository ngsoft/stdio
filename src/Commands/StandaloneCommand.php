<?php

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Interfaces\Command, Commands\Interfaces\Parser, STDIO
};
use RuntimeException;

class StandaloneCommand implements Command {

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description = '';

    /** @var Option[] */
    protected $options = [];

    /** @var callable|null */
    protected $callback;

    /** @var Parser */
    protected $parser;

    /**
     * Run the command
     * @param array|null $args if not set will use $argv
     */
    public function run(?array $args = null) {
        ErrorHandler::register();
        global $argv;
        if (!is_array($args)) {
            $args = $argv;
            array_shift($args);
        }

        $arguments = $this->parser->parseArguments($args, $this->getOptions());
        $this->command($arguments);
    }

    /** @param callable $callback */
    public function __construct(callable $callback) {
        $this->options = [
            BooleanOption::create('help', '-h', '--help')
        ];
        $this->setCallback($callback);
        $this->parser = new CommandParser();
    }

    /**
     * Creates a new instance
     * @param callable $callback Callable to use when using run()
     * @return static
     */
    public static function create(callable $callback): self {
        return new static($callback);
    }

    /** {@inheritdoc} */
    public function command(array $args) {
        $io = STDIO::create();
        $retval = call_user_func_array($this->callback, [$args, $io]);
        if (is_string($retval)) {
            $io($retval);
        }
    }

    /** {@inheritdoc} */
    public function getDescription(): string {
        return $this->description;
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * Adds a single Option
     * @param Option $option
     * @return static
     */
    public function addOption(Option $option): self {
        $this->options[] = $option;
        return $this;
    }

    /**
     * Set Command Name
     * @param string $name
     * @return static
     */
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * Set Command Description
     * @param string $description
     * @return static
     */
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Set Multiple Option
     * @param array $options
     * @return static
     * @throws RuntimeException
     */
    public function setOptions(array $options): self {
        $this->options = [];
        foreach ($options as $opt) {
            if (!($opt instanceof Option)) {
                throw new RuntimeException('Invalid Option supplied.');
            }
            $this->addOption($opt);
        }
        return $this;
    }

    /**
     * Set Callback to use for the command
     * @param callable $callback
     * @return static
     */
    public function setCallback(callable $callback): self {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Set Custom Command Parser
     * @param Parser $parser
     * @return static
     */
    public function setParser(Parser $parser): self {
        $this->parser = $parser;
        return $this;
    }

}
