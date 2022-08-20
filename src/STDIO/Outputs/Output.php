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

class Output implements OutputInterface
{

    /** @var resource */
    protected $stream;
    protected ?Cursor $cursor = null;

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

            if (false === @fwrite($this->stream, $this->formatter->format($line))) {
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
     * Get Cursor for output
     */
    public function getCursor(): Cursor
    {

        // use null formatter for better performances (no pcre)
        if ( ! $this->cursor) {
            $out = $this;
            if ($this->formatter instanceof NullFormatter === false) {
                $out = clone $this;
                $out->formatter = new NullFormatter();
            }
            $this->cursor = new Cursor($out);
        }

        return $this->cursor;
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
