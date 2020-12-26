<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Helpers\Hello, Commands\Helpers\Help, Commands\Interfaces\Command, Commands\Interfaces\Parser, STDIO
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface, Http\Server\RequestHandlerInterface
};
use RuntimeException;

/**
 * Command Middleware
 * Put in last position
 */
class CommandMiddleware implements MiddlewareInterface {

    /** @var ContainerInterface */
    protected $container;

    /** @var ResponseFactoryInterface */
    protected $responsefactory;

    /** @var Parser|null */
    protected $parser;

    /** @var ErrorHandler|null */
    protected $errorHandler;

    /** @var bool */
    protected $displayTraceOnError = false;

    /** @var array<string,string> */
    protected $commands = [
        '__default' => Help::class,
        'help' => Help::class,
        'hello' => Hello::class,
    ];

    /**
     * Tells the ErrorHandler if it can display trace on error
     * @param bool $displayTraceOnError
     * @return static
     */
    public function displayTraceOnError(bool $displayTraceOnError): self {
        $this->displayTraceOnError = $displayTraceOnError;
        return $this;
    }

    /**
     * Set a custom Argument Parser
     * @param Parser $parser
     * @return static
     */
    public function setArgumentParser(Parser $parser) {
        $this->parser = $parser;
        return $this;
    }

    public function __construct(
            ContainerInterface $container,
            ResponseFactoryInterface $responsefactory
    ) {
        $this->container = $container;
        $this->responsefactory = $responsefactory;
        $this->parser = new CommandParser();
        if ($container->has('commands')) {
            $commands = $container->get('commands');
            if (is_array($commands)) {
                foreach ($commands as $command => $classname) {
                    if (
                            is_string($command)
                            and class_exists($classname)
                    ) {
                        if (in_array(Command::class, class_implements($classname))) {
                            $this->commands[$command] = $classname;
                        }
                    }
                }
            }
        }
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        //not in cli mode
        if (php_sapi_name() !== 'cli') return $handler->handle($request);
        $this->errorHandler = ErrorHandler::register();
        $this->errorHandler->setDisplayTrace($this->displayTraceOnError);

        global $argv;
        $io = STDIO::create();

        $args = $argv;
        array_shift($args); //removes scriptname
        $command = array_shift($args);
        if ($command === null) $command = '__default';


        if (!isset($this->commands[$command])) {
            throw new RuntimeException("Command $command does not exists");
        }
        $classname = $this->commands[$command];
        /** @var Command $task */
        $task = $this->container->get($classname);
        if ($classname == Help::class) {
            /** @var Help $task */
            foreach ($this->commands as $commandName => $commandClassname) {
                if ($commandName === '__default') continue;
                $task->addCommand($this->container->get($commandClassname), $commandName);
            }
        }
        $arguments = $this->parser->parseArguments($args, $task->getOptions());

        if (
                ($result = $task->command($arguments))
                and is_string($result)
        ) $io->out($result); //Basic Render (for simple commands)


        return $this->responsefactory->createResponse(200); //retuns empty response
    }

}
