<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use RuntimeException,
    Stringable,
    TypeError;

class Output implements OutputInterface {

    /** @var resource */
    private $stream;

    public function __construct() {
        $this->stream = fopen('php://stdout', 'w'); ;
    }

    /** {@inheritdoc} */
    public function write(string|Stringable|array $message): void {
        $messages = is_array($message) ? $message : [$message];

        foreach ($messages as $line) {
            if ($line instanceof Stringable) $line = $line->__toString();


            if (!is_string($line)) {
                throw new TypeError(sprintf('Invalid message type %s.', get_debug_type($line)));
            }

            $this->flushStream($line);
        }
    }

    protected function flushStream(string $message) {
        if (false === @fwrite($this->stream, $message)) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->stream);
    }

}
