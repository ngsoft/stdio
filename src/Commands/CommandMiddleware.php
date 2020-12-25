<?php

namespace NGSOFT\Commands;

use NGSOFT\{
    Commands\Helpers\Help, Commands\Interfaces\Command, STDIO
};
use Psr\{
    Container\ContainerInterface, Http\Message\ResponseFactoryInterface, Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface, Http\Server\MiddlewareInterface, Http\Server\RequestHandlerInterface
};
use RuntimeException,
    Throwable;

/**
 * Command Middleware
 * Put in last position
 */
class CommandMiddleware implements MiddlewareInterface {

    /** @var ContainerInterface */
    protected $container;

    /** @var ResponseFactoryInterface */
    protected $responsefactory;

    /** @var array<string,string> */
    protected $commands = [
        '__default' => Help::class,
        'help' => Help::class,
    ];

    public function __construct(
            ContainerInterface $container,
            ResponseFactoryInterface $responsefactory
    ) {

        $this->container = $container;
        $this->responsefactory = $responsefactory;
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
        global $argv;

        $io = STDIO::create();
        $stderr = $io->getOutput('err');
        $errorHandler = $io
                ->createRect()
                ->setBackground('red')
                ->setColor('white');



        $args = $argv;
        array_shift($args); //removes scriptname
        $command = array_shift($args);
        if ($command === null) $command = '__default';
        try {

            if (!isset($this->commands[$command])) {
                throw new RuntimeException("Command $command does not exists");
            }
            $classname = $this->commands[$command];
            /** @var Command $task */
            $task = $this->container->get($classname);
            if ($classname == Help::class) {
                $commands = [];
                foreach ($this->commands as $commandName => $commandClassname) {

                    if ($commandClassname !== Help::class) {
                        $commands[$commandName] = $this->container->get($commandClassname);
                    }
                }
                $task->setCommands($commands);
            }
            $arguments = $task->parseArguments($args);

            if (
                    ($result = $task->command($arguments))
                    and is_string($result)
            ) $io->out($result); //Basic Render (for simple commands)
        } catch (Throwable $error) {
            //command has thrown an error
            $errorHandler->write($error->getMessage());
            $errorHandler->render($stderr);
        }

        return $this->responsefactory->createResponse(200); //retuns empty response
    }

}
