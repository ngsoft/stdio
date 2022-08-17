<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

interface OutputInterface
{

    /**
     * Write message to the output
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return void
     * @throws TypeError
     */
    public function write(string|Stringable|array $message): void;

    /**
     * Write message to the output and creates a new line
     *
     * @param string|Stringable|array $message
     * @return void
     */
    public function writeln(string|Stringable|array $message): void;
}
