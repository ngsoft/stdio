<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\Commands\{
    Helpers\Help, Interfaces\Parser
};
use RuntimeException,
    Throwable;

class StandaloneCommand extends CommandAbstract {

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $description = '';

    /** @var Option[] */
    protected $options = [];

    /** @var bool */
    protected $displayHelpOnError;

    /** @var callable|null */
    protected $callback;

    /** @var Parser */
    protected $parser;

    /**
     * Run the command
     * @param array|null $args if not set will use $argv
     * @return mixed
     */
    public function run(array $args = null) {
        global $argv;
        static $help;
        /** @var Help $help */
        $help = $help ?? new Help();
        if (!is_array($args)) {
            $args = $argv;
            array_shift($args);
        }
        $displayHelp = false;
        try {
            $arguments = $this->parser->parseArguments($args, $this->getOptions());
            if ($displayHelp = $arguments['help'] ?? $displayHelp) {
                $help->renderFor($this);
                return true;
            }
            $retval = $this->command($arguments);
        } catch (Throwable $error) {
            ErrorHandler::handle($error);
            if (!$displayHelp and $this->displayHelpOnError) {
                try {
                    $help->renderFor($this);
                } catch (Throwable $err) { $err->getCode(); }
            }
            return false;
        }

        return $retval;
    }

    /** @param callable $callback */
    public function __construct(callable $callback, bool $displayHelpOnError = false) {
        $this->displayHelpOnError = $displayHelpOnError;
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
     *
     * @param callable $callback
     * @param bool $displayHelpOnError
     * @return static
     */
    public static function create(callable $callback, bool $displayHelpOnError = false) {
        return new static($callback, $displayHelpOnError);
    }

    /** {@inheritdoc} */
    public function command(array $args) {
        $io = $this->getSTDIO();
        return call_user_func_array($this->callback, [$args, $io]);
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

    /**
     * Displays help on error
     *
     * @param bool $displayHelpOnError
     * @return static
     */
    public function setDisplayHelpOnError(bool $displayHelpOnError = true) {
        $this->displayHelpOnError = $displayHelpOnError;
        return $this;
    }

}
