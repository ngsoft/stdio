<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use Countable,
    IteratorAggregate,
    RuntimeException,
    Stringable,
    Traversable,
    TypeError;

class Buffer implements Countable, IteratorAggregate
{

    /** @var string[] */
    private $buffer = [];

    /**
     * Adds Message to the buffer
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return void
     * @throws RuntimeException
     */
    public function write(string|Stringable|array $message): void
    {
        foreach ((array) $message as $line) {
            if ( ! is_string($line) && $line instanceof \Stringable === false) {
                throw new TypeError(sprintf('Invalid message type %s.', get_debug_type($line)));
            }
            $this->buffer[] = (string) $line;
        }
    }

    /**
     * Write message to the buffer and creates a new line
     *
     * @param string|Stringable|array $message
     * @return void
     */
    public function writeln(string|Stringable|array $message): void
    {
        $this->write($message);
        $this->write("\n");
    }

    public function clear(): void
    {
        $this->buffer = [];
    }

    /**
     * Flush buffer into Output
     *
     * @param Output $output
     * @return void
     */
    public function flush(Output $output): void
    {
        foreach ($this as $line) {
            $output->write($line);
        }
        $this->clear();
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->buffer);
    }

    public function getIterator(): Traversable
    {
        $buffer = $this->buffer;
        $this->clear();
        yield from $buffer;
    }

}
