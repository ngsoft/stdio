<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use Countable,
    IteratorAggregate,
    RuntimeException,
    Stringable;

class Buffer implements Countable, IteratorAggregate {

    /** @var string[] */
    private $buffer = [];

    /**
     * Adds Message to the buffer
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return void
     * @throws RuntimeException
     */
    public function write(string|Stringable|array $message): void {

        $message = is_array($message) ? $message : [$message];

        foreach ($message as $line) {
            if (!is_string($line) && $line instanceof \Stringable === false) throw new \TypeError(sprintf('Invalid message type %s.', get_debug_type($line)));
            $this->buffer[] = $line;
        }
    }

    public function clear(): void {
        $this->buffer = [];
    }

    /**
     * Flush buffer into Output
     *
     * @param Output $output
     * @return void
     */
    public function flush(Output $output): void {
        foreach ($this as $line) {
            $output->write($line);
        }
        $this->clear();
    }

    /** {@inheritdoc} */
    public function count(): int {
        return count($this->buffer);
    }

    public function getIterator(): \Traversable {
        $buffer = $this->buffer;
        foreach ($buffer as $line) yield $line;
    }

}
