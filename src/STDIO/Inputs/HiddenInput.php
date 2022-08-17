<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Inputs;

use NGSOFT\STDIO\{
    Outputs\Output, Utils\Utils
};
use function str_starts_with;

class HiddenInput extends Input
{

    protected const WINDOWS_BINARY_PATH = '../../../bin/hiddeninput.exe';

    protected string|false $binary = false;
    protected Output $output;

    public function __construct()
    {
        parent::__construct();

        $this->output = new Output();

        if (Utils::isWindows()) {
            $bin = __DIR__ . DIRECTORY_SEPARATOR . self::WINDOWS_BINARY_PATH;
            if (str_starts_with($bin, 'phar:')) {
                $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($bin);
                if (is_file($tmp) || @copy($bin, $tmp)) {
                    $bin = $tmp;
                } else { $bin = false; }
            }

            $this->binary = ! $bin ? false : realpath($bin);
        }
    }

    public function readln(bool $allowEmptyline = true): string
    {

        $out = $this->output;

        $result = false;

        if ($this->binary) {
            while ($result === false) {
                $line = shell_exec(sprintf('"%s"', $this->binary));
                $line = is_string($line) ? rtrim($line, "\n\r") : '';
                $out->writeln('');
                if ( ! $allowEmptyline && empty($line)) {
                    continue;
                }
                $result = empty($line) ? '' : $line;
            }
            return $result;
        } elseif (Utils::supportsSTTY() && $mode = @shell_exec('stty -g')) {

            shell_exec('stty -echo');

            while ($result === false) {
                $line = fgets($this->stream, 4096);
                $out->writeln('');

                $line = rtrim($line, "\n\r");
                if ( ! $allowEmptyline && empty($line)) {
                    continue;
                }

                $result = empty($line) ? '' : $line;
            }

            shell_exec(sprintf('stty %s', $mode));
            return $result;
        }

        return parent::readln($allowEmptyline);
    }

}
