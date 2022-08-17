<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use Countable,
    IteratorAggregate,
    RuntimeException,
    Stringable,
    Traversable,
    TypeError;
use function get_debug_type;

class Buffer implements Countable, IteratorAggregate, Renderer, \Stringable, OutputInterface
{

    /** @var string[] */
    private $buffer = [];

    public function render(OutputInterface $output): void
    {
        $this->flush($output);
    }

    /**
     * Adds Message to the buffer
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return void
     * @throws RuntimeException
     */
    public function write(string|Stringable|array $message): void
    {

        if ( ! is_array($message)) {
            $message = [$message];
        }

        foreach ($message as $line) {
            if (( ! is_string($line) && $line instanceof \Stringable === false) || $line instanceof self) {
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
     * Pull and erase the buffer
     * @return array
     */
    public function pull(): array
    {
        try {
            return $this->buffer;
        } finally {
            $this->clear();
        }
    }

    /**
     * Flush buffer into Output
     *
     * @param Output $output
     * @return void
     */
    public function flush(Output $output): void
    {
        $output->write($this->pull());
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->buffer);
    }

    public function getIterator(): Traversable
    {
        yield from $this->buffer;
    }

    public function __toString(): string
    {
        return implode('', $this->pull());
    }

    public function __debugInfo(): array
    {
        return $this->buffer;
    }

}
