<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\STDIO,
    Throwable;

class ErrorHandler {

    /** @var bool */
    protected $displayTrace = false;

    /**
     * Respond to an exception
     * @param Throwable $error
     */
    public function __invoke(Throwable $error) {

        $io = STDIO::create();
        $stderr = $io->getOutput('err');

        $io
                ->yellow("Error:")
                ->linebreak()
                ->err();

        $handler = $io->createRect()
                ->setBackground('red')
                ->setColor('white');
        $handler->write($error->getMessage());
        $handler->render($stderr);
        if ($this->displayTrace) {
            $io->yellow("\nTrace:\n")->err();
            $io->err($error->getTraceAsString());
        }
    }

    /**
     * Display Trace on error
     * @param bool $displayTrace
     * @return static
     */
    public function setDisplayTrace(bool $displayTrace): self {
        $this->displayTrace = $displayTrace;
        return $this;
    }

    /**
     * Register to the exception handler
     * @staticvar type $instance
     * @return static
     */
    public static function register(): self {
        static $instance;
        if (!($instance instanceof self)) {
            $instance = new static();
            set_exception_handler($instance);
        }
        return $instance;
    }

    /**
     * Unregister
     */
    public static function unregister() {
        set_exception_handler(null);
    }

}
