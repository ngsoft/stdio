<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\Commands\{
    Helpers\Help, Interfaces\Command, Interfaces\Parser
};
use RuntimeException,
    Throwable;

class StandaloneCommand extends CommandAbstract implements Command {

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

        try {
            $arguments = $this->parser->parseArguments($args, $this->getOptions());
            $this->command($arguments);
        } catch (Throwable $error) {

            try {
                $help = new Help();
                $help->renderFor($this);
            } catch (Throwable $err) { $err->getCode(); }
            throw $error;
        }
    }

    /** @param callable $callback */
    public function __construct(callable $callback) {
        $this->options = [
                    BooleanOption::create('help', '-h', '--help')
                    ->setDescription('Display this help message')
        ];
        $this->setCallback($callback);
        $this->parser = new CommandParser();
        parent::__construct();
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

        if (
                array_key_exists('help', $args)
                and $args['help'] === true
        ) {

            $help = new Help();
            return $help->renderFor($this);
        }

        $io = $this->getSTDIO();
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
