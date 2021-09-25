<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use NGSOFT\STDIO,
    Throwable;

class ErrorHandler {

    /** @var bool */
    protected $displayTrace = false;

    /** @var ErrorHandler */
    protected static $instance;

    /**
     * Respond to an exception
     * @param Throwable $error
     */
    public function __invoke(Throwable $error) {

        $io = STDIO::create();
        $stderr = $io->getSTDERR();

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
     * Handles Error
     * @param Throwable $error
     * @param bool $displayTrace
     */
    public static function handle(Throwable $error, bool $displayTrace = false) {
        self::$instance = self::$instance ?? new static();
        $handler = self::$instance;
        $handler->setDisplayTrace($displayTrace);
        $handler($error);
    }

    /**
     * Register to the exception handler
     * @staticvar type $instance
     * @return static
     */
    public static function register(): self {

        if (!(static::$instance instanceof self)) {
            static::$instance = new static();
            set_exception_handler([static::$instance, '__invoke']);
        }
        return static::$instance;
    }

    /**
     * Unregister
     */
    public static function unregister() {
        set_exception_handler(null);
    }

}
