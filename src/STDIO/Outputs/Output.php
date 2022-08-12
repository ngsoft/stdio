<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Formatters\{
    FormatterInterface, TagFormatter
};
use RuntimeException,
    Stringable,
    TypeError;

class Output
{

    /** @var resource */
    protected $stream;

    /** @var FormatterInterface */
    protected $formatter;

    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new TagFormatter();
        $this->stream = fopen('php://stdout', 'w+'); ;
    }

    /**
     * Write message to the output
     *
     * @param string|Stringable|array $message
     * @return void
     * @throws TypeError
     */
    public function write(string|Stringable|array $message): void
    {
        $messages = is_array($message) ? $message : [$message];

        foreach ($messages as $line) {
            if ($line instanceof Stringable) $line = $line->__toString();

            if ( ! is_string($line)) {
                throw new TypeError(sprintf('Invalid message type %s.', get_debug_type($line)));
            }

            $line = $this->formatter->format($message);

            $this->flushStream($line);
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

    protected function flushStream(string $message)
    {
        if (false === fwrite($this->stream, $message)) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->stream);
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
