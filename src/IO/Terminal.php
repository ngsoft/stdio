<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO;

class Terminal {

    /** @var int|null */
    private static $width;

    /** @var int|null */
    private static $height;

    public function __construct() {
        if (php_sapi_name() !== "cli") throw new \RuntimeException("Can only be run under CLI Environnement");
    }

    /**
     * Get Terminal Width
     * @return int
     */
    public function getWidth(): int {

        $width = getenv('COLUMNS');
        if ($width !== false) {
            return (int) trim($width);
        }
        if (self::$width === null) {

            $width = @exec('tput cols 2>&1');
            if (is_numeric($width)) self::$width = (int) $width;
        }
        return self::$width ?: 80;
    }

    /**
     * Get Terminal Height
     * @return int
     */
    public function getHeight(): int {

        $height = getenv('LINES');
        if ($height !== false) {
            return (int) trim($height);
        }
        if (self::$height === null) {

            $height = @shell_exec('tput lines 2>&1');
            if (is_numeric($height)) self::$height = (int) $height;
        }

        return self::$height ?: 50;
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
        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }
        $stream = fopen("php://stdout", "w");
        if (DIRECTORY_SEPARATOR === '\\') {
            return
                    (function_exists('sapi_windows_vt100_support') and @ sapi_windows_vt100_support($stream))
                    or false !== getenv('ANSICON')
                    or 'ON' === getenv('ConEmuANSI')
                    or in_array(getenv('TERM'), ['xterm', 'cygwin']);
        }
        if (function_exists('stream_isatty')) return @stream_isatty($stream);

        if (function_exists('posix_isatty')) return @posix_isatty($stream);

        $stream = fopen("php://stdout", "w");
        $stat = @fstat($stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

}
