<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Inputs;

class Input
{

    /** @var resource */
    protected $stream;

    public function __construct()
    {
        $this->stream = fopen('php://stdin', 'r+');
    }

    /**
     * Input Stream
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Read lines from the input
     *
     * @param int $lines Number of lines to read
     * @param bool $allowEmptyLines
     * @return string[]
     */
    public function read(int $lines = 1, bool $allowEmptyLines = true): array
    {
        $result = [];

        while (count($result) < $lines) {
            $line = fgets($this->stream);
            $line = rtrim($line, "\r\n");
            if ( ! $allowEmptyLines && empty($line)) {
                continue;
            }
            $result[] = $line;
        }

        return $result;
    }

}
