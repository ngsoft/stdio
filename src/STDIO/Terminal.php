<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use RuntimeException;

/**
 * @property-read int $width Terminal Width
 * @property-read int $height Terminal Height
 */
final class Terminal {

    /**
     * Get unique instance
     * @staticvar Terminal $instance
     * @return static
     */
    public static function create(): self {
        static $instance;
        $instance = $instance ?? new static();
        return $instance;
    }

    private static function readFromProcess(string $command): ?string {

        if (!function_exists('proc_open')) {
            return null;
        }


        $process = @proc_open(
                        $command,
                        [
                            1 => ['pipe', 'w'],
                            2 => ['pipe', 'w'],
                        ],
                        $pipes,
                        null,
                        null,
                        ['suppress_errors' => true]
        );
        if (!\is_resource($process)) {
            return null;
        }

        $result = stream_get_contents($pipes[1]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        return $result;
    }

    private static function getSize(): ?string {
        if (DIRECTORY_SEPARATOR === '\\') {
            if ($out = self::readFromProcess('mode con /status')) {
                $out = explode("\n", $out);
                $result = [];
                foreach ([$out[3], $out[4]] as $line) {
                    if (preg_match('/(\d+)/', $line, $matches) !== false) $result[] = $matches[1];
                }

                return implode(' ', $result);
            }
        }
        return self::readFromProcess('stty size 2>&1');
    }

    public function __construct() {
        if (php_sapi_name() !== "cli") throw new RuntimeException("Can only be run under CLI Environnement");
    }

    /**
     * Get Terminal Width
     * @return int
     */
    public function getWidth(): int {
        if ($width = getenv('COLUMNS')) return (int) trim($width);
        if ($out = self::getSize()) {
            $list = explode(' ', $out);
            return (int) trim($list[1]);
        }
        return 80;
    }

    /**
     * Get Terminal Height
     * @return int
     */
    public function getHeight(): int {
        if ($height = getenv('LINES')) return (int) trim($height);
        if ($out = self::getSize()) {
            $list = explode(' ', $out);
            return (int) trim($list[0]);
        }
        return 24;
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
    public function hasColorSupport(): bool {

        static $result;

        if (is_null($result)) {
            if (getenv('NOCOLOR') !== false) return $result = false;

            $stream = fopen("php://stdout", "w");
            if (DIRECTORY_SEPARATOR === '\\') {
                return $result = (function_exists('sapi_windows_vt100_support') and @ sapi_windows_vt100_support($stream))
                        or false !== getenv('ANSICON')
                        or 'ON' === getenv('ConEmuANSI')
                        or preg_match('/^(cygwin|xterm)/', getenv('TERM') ?: '') !== false;
            }
            if (function_exists('stream_isatty')) return $result = @stream_isatty($stream);
            if (function_exists('posix_isatty')) return $result = @posix_isatty($stream);
            $stat = @fstat($stream);
            // Check if formatted mode is S_IFCHR
            return $result = $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
        }

        return $result;
    }

    public function __get($name) {
        if (!in_array($name, ['width', 'height'])) throw new RuntimeException("Invalid property $name.");
        $method = sprintf('get%s', ucfirst($name));
        return $this->{$method}();
    }

}
