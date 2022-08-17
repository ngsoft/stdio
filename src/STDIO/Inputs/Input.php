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


            $result[] = $this->readln($allowEmptyLines);
        }

        return $result;
    }

    public function readln(bool $allowEmptyline = false): string|false
    {

        $cp = 0;
        $result = false;

        if (function_exists('sapi_windows_cp_set')) {
            $cp = sapi_windows_cp_get();
            sapi_windows_cp_set(sapi_windows_cp_get('oem'));
        }

        try {

            while (false === $result) {
                $line = fgets($this->stream, 4096);
                $line = rtrim($line, "\r\n");
                if (empty($line) && ! $allowEmptyline) {
                    continue;
                }
                $result = $line;
            }
        } catch (\Throwable) {
            $result = false;
        }

        if (0 !== $cp) {
            sapi_windows_cp_set($cp);

            if ( ! empty($result)) {
                $result = sapi_windows_cp_conv(sapi_windows_cp_get('oem'), $cp, $result) ?? false;
            }
        }


        return $result;
    }

}
