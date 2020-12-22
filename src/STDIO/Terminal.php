<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use RuntimeException;

/**
 * @property-read int $width Terminal Width
 * @property-read int $height Terminal Height
 */
class Terminal {

    public function __construct() {
        if (php_sapi_name() !== "cli") throw new RuntimeException("Can only be run under CLI Environnement");
    }

    /**
     * Get Terminal Width
     * @return int
     */
    public function getWidth(): int {
        if ($width = getenv('COLUMNS')) return (int) trim($width);

        if (DIRECTORY_SEPARATOR === '\\') { // Windows
            @exec('mode con /status', $out, $retval);
            if ($retval == 0) {
                $line = $out[4];
                if (preg_match('/(\d+)/', $line, $matches) !== false) return (int) $matches[1];
            }
        } else {
            @exec('stty size 2>&1', $out, $retval);
            if ($retval == 0) {
                $list = explode(' ', $out[0]);
                return (int) trim($list[1]);
            }
        }

        return 120;
    }

    /**
     * Get Terminal Height
     * @return int
     */
    public function getHeight(): int {
        if ($height = getenv('LINES')) return (int) trim($height);
        if (DIRECTORY_SEPARATOR === '\\') { // Windows
            @exec('mode con /status', $out, $retval);
            if ($retval == 0) {
                $line = $out[4];
                if (preg_match('/(\d+)/', $line, $matches) !== false) return (int) $matches[1];
            }
        } else {
            @exec('stty size 2>&1', $out, $retval);
            if ($retval == 0) {
                $list = explode(' ', $out[0]);
                return (int) trim($list[0]);
            }
        }

        return 30;
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     * This is tricky on Windows, because Cygwin, Msys2 etc emulate pseudo
     * terminals via named pipes, so we can only check the environment.
     *
     * Reference: Composer\XdebugHandler\Process::supportsColor
     * @link https://github.com/composer/xdebug-handler
     * @link https://github.com/symfony/console/blob/master/Output/StreamOutput.php
     *
     * @return bool true if the stream supports colorization, false otherwise
     */
    public function hasColorSupport() {
        $stream = fopen("php://stdout", "w");
        if (DIRECTORY_SEPARATOR === '\\') {
            return
                    (function_exists('sapi_windows_vt100_support') and @ sapi_windows_vt100_support($stream))
                    or false !== getenv('ANSICON')
                    or 'ON' === getenv('ConEmuANSI')
                    or preg_match('/^(cygwin|xterm)/', getenv('TERM') ?: '') !== false;
        }
        if (function_exists('stream_isatty')) return @stream_isatty($stream);
        if (function_exists('posix_isatty')) return @posix_isatty($stream);
        $stat = @fstat($stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

    public function __get($name) {
        if (!in_array($name, ['width', 'height'])) throw new RuntimeException("Invalid index $name.");
        $method = sprintf('get%s', ucfirst($name));
        return $this->{$method}();
    }

}
