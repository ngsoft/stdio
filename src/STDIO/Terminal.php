<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\Utils\Utils,
    RuntimeException;

/**
 * @property-read int $width Terminal Width
 * @property-read int $height Terminal Height
 */
final class Terminal {

    public readonly bool $colors;
    public readonly bool $tty;

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

    private static function getSize(): ?string {
        if (DIRECTORY_SEPARATOR === '\\') {
            if ($out = Utils::executeProcess('mode con /status')) {
                $out = explode("\n", $out);
                $result = [];
                foreach ([$out[3], $out[4]] as $line) {
                    if (preg_match('/(\d+)/', $line, $matches) !== false) $result[] = $matches[1];
                }

                return implode(' ', $result);
            }
        }
        return Utils::executeProcess('stty size 2>&1');
    }

    public function __construct() {
        if (php_sapi_name() !== "cli") throw new RuntimeException("Can only be run under CLI Environnement");
        $this->colors = $this->hasColorSupport();
        $this->tty = $this->supportsTTY();
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
    private function hasColorSupport(): bool {

        static $result;

        if (is_null($result)) {
            if (getenv('NOCOLOR') !== false) return $result = false;

            $stream = fopen("php://stdout", "w");
            if (DIRECTORY_SEPARATOR === '\\') {
                return
                        $result = preg_match('/^(cygwin|xterm)/', getenv('TERM') ?: '') > 0 or
                        false !== getenv('ANSICON') or
                        'ON' === getenv('ConEmuANSI') or
                        (function_exists('sapi_windows_vt100_support') and @sapi_windows_vt100_support($stream));
            }
            if (function_exists('stream_isatty')) return $result = @stream_isatty($stream);
            if (function_exists('posix_isatty')) return $result = @posix_isatty($stream);
            $stat = @fstat($stream);
            // Check if formatted mode is S_IFCHR
            return $result = $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
        }

        return $result;
    }

    /**
     * Returns true if the terminal supports tty.
     *
     * @staticvar type $supported
     * @return bool
     */
    private function supportsTTY(): bool {
        static $supported;

        if (null === $supported) {
            if (function_exists('proc_open')) {
                $supported = (bool) proc_open(
                                'echo 1 >/dev/null',
                                [
                                    ['file', '/dev/tty', 'r'],
                                    ['file', '/dev/tty', 'w'],
                                    ['file', '/dev/tty', 'w']
                                ], $pipes,
                                null,
                                null,
                                ['suppress_errors' => true]
                );
            } else $supported = false;
        }

        return $supported;
    }

    public function __isset($name) {
        return method_exists($this, sprintf('get%s', ucfirst($name)));
    }

    public function __get(string $name): mixed {
        $method = sprintf('get%s', ucfirst($name));
        if (!method_exists($this, $method)) throw new RuntimeException("Invalid property $name.");
        return call_user_func([$this, $method]);
    }

    public function __set(string $name, mixed $value) {

    }

    public function __unset(string $name) {

    }

    public function __debugInfo() {
        return [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'colors' => $this->colors,
            'tty' => $this->tty,
        ];
    }

}
