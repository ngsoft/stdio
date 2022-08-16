<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Formatters\{
    Formatter, NullFormatter
};
use RuntimeException,
    Stringable,
    TypeError;
use function get_debug_type;

class Output
{

    /** @var resource */
    protected $stream;

    public function __construct(protected ?Formatter $formatter = null)
    {
        $this->stream ??= fopen('php://stdout', 'w+');
        $this->formatter ??= new NullFormatter();
    }

    /**
     * Write message to the output
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return void
     * @throws TypeError
     */
    public function write(string|Stringable|array $message): void
    {

        if ( ! is_array($message)) {
            $message = [$message];
        }


        foreach ($message as $line) {



            if ( ! is_string($line) && ! ($line instanceof Stringable)) {
                throw new TypeError(sprintf('Invalid message type %s.', get_debug_type($line)));
            }


            if (false === @fwrite($this->stream, $this->formatter->format((string) $line))) {
                throw new RuntimeException('Unable to write output.');
            }
            fflush($this->stream);
        }
    }

    /**
     * Write message to the output and creates a new line
     *
     * @param string|Stringable|array $message
     * @return void
     */
    public function writeln(string|Stringable|array $message): void
    {
        $this->write($message);
        $this->write("\n");
    }

    /**
     * Output Stream
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

}
