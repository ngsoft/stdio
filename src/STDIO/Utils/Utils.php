<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use InvalidArgumentException,
    NGSOFT\Tools,
    Throwable;
use function preg_test;

class Utils
{

    public static function executeProcess(string $command): ?string
    {

        if ( ! function_exists('proc_open')) {
            return null;
        }


        try {
            Tools::errors_as_exceptions();

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

            if ( ! \is_resource($process)) {
                return null;
            }

            $result = stream_get_contents($pipes[1]);

            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return $result;
        } catch (Throwable) {
            return null;
        } finally { restore_error_handler(); }
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
    public static function supportsColors(): bool
    {

        static $result;

        if (is_null($result)) {
            if (getenv('NOCOLOR') !== false) { return $result = false; }

            $stream = fopen("php://stdout", "w");
            if (DIRECTORY_SEPARATOR === '\\') {
                return
                        $result = preg_match('/^(cygwin|xterm)/', getenv('TERM') ?: '') > 0 ||
                        false !== getenv('ANSICON') ||
                        'ON' === getenv('ConEmuANSI') ||
                        (function_exists('sapi_windows_vt100_support') and @sapi_windows_vt100_support($stream));
            }
            if (function_exists('stream_isatty')) { return $result = @stream_isatty($stream); }
            if (function_exists('posix_isatty')) { return $result = @posix_isatty($stream); }
            $stat = @fstat($stream);
            // Check if formatted mode is S_IFCHR
            return $result = $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
        }

        return $result;
    }

    /**
     * Returns true if the terminal supports tty.
     */
    public static function supportsTTY(): bool
    {
        static $supported;

        if (is_null($supported)) {
            $supported = false;
            if (function_exists('proc_open')) {

                try {
                    Tools::errors_as_exceptions();
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
                } catch (Throwable) {
                    $supported = false;
                } finally { restore_error_handler(); }
            }
        }

        return $supported;
    }

    /**
     * Get Number of colors supported
     * @return int
     */
    public static function getNumColorSupport(): int
    {

        static $result;

        if (is_null($result)) {
            $result = 8;

            if ('truecolor' === getenv('COLORTERM')) {
                $result = 16777215;
            } elseif (preg_match('/(cygwin|xterm|256)/', getenv('TERM') ?: '')) {
                $result = 256;
            } elseif ($value = self::executeProcess('tput colors')) {
                $result = intval($value);
            }
        }

        return $result;
    }

    /**
     * Run on windows
     */
    public static function isWindows(): bool
    {
        static $result;
        return $result ??= DIRECTORY_SEPARATOR === '\\';
    }

    public static function supportsPowershell(): bool
    {
        static $result;
        return $result ??= self::isWindows() && ! empty(self::executeProcess('powershell.exe -?'));
    }

    public static function supportsSTTY(): bool
    {
        static $result;
        return $result ??= ! self::isWindows() && ! empty(self::executeProcess('stty'));
    }

    public static function isHexColor(string $color): bool
    {
        return preg_test('/^#?(?:[0-9A-F]{3}){1,2}$/i', $color);
    }

    /**
     * Convert hex color to ansi code
     */
    public static function convertHexToAnsi(string $hexColor, bool $isBackgroundColor = false, bool $isGrayscale = false): string
    {

        static $mode;

        if ( ! self::isHexColor($hexColor)) {
            throw new InvalidArgumentException(sprintf('Invalid "%s" color.', $hexColor));
        }

        if ( ! $mode) {
            $mode = 'ansi';
            if ($isGrayscale && self::getNumColorSupport() >= 256) {
                $mode = 'gray';
            } elseif (self::getNumColorSupport() > 256) {
                $mode = 'truecolor';
            } elseif (self::getNumColorSupport() >= 256) {
                $mode = '256color';
            }
        }

        $color = ltrim($hexColor, '#');

        if (3 === strlen($color)) {
            $color = $color[0] . $color[0] . $color[1] . $color [1] . $color[2] . $color[2];
        }

        [$red, $green, $blue] = array_map(fn($hex) => intval($hex, 16), str_split($color, 2));

        if ($mode === 'gray') {
            return sprintf('%d8;5;%d', $isBackgroundColor ? 4 : 3, self::degradeToGrayscale($red, $green, $blue));
        } elseif ($mode === '256color') {
            return sprintf('%d8;5;%d', $isBackgroundColor ? 4 : 3, self::degradeTo256($red, $green, $blue));
        } elseif ($mode === 'ansi') {
            return sprintf('%d%d', $isBackgroundColor ? 4 : 3, self::degradeToAnsi($red, $green, $blue));
        }

        return sprintf('%d8;2;%d;%d;%d', $isBackgroundColor ? 4 : 3, $red, $green, $blue);
    }

    protected static function degradeToGrayscale(int $red, int $green, int $blue): int
    {
        static $table = [8, 18, 28, 38, 48, 58, 68, 78, 88, 98, 108, 118, 128, 138, 148, 158, 168, 178, 188, 198, 208, 218, 228, 238];

        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);
        $middle = (int) floor((($max - $min) / 2) + $min);

        if ($middle > 238) {
            return 231;
        } elseif ($middle < 8) {
            return 16;
        }


        foreach ($table as $level => $intensity) {

            if ($middle < $intensity) {
                break;
            }
        }

        return 232 + $level;
    }

    /**
     * Find nearest 256 from table
     */
    protected static function degradeTo256(int $red, int $green, int $blue): int
    {
        static $table = [0, 95, 135, 175, 215, 255];

        $lRed = $lGreen = $lBlue = 0;

        foreach ($table as $level => $intensity) {

            if ($red >= $intensity) {
                $lRed = $level;
            }

            if ($green >= $intensity) {
                $lGreen = $level;
            }

            if ($blue >= $intensity) {
                $lBlue = $level;
            }
        }

        return 16 + (36 * $lRed) + (6 * $lGreen) + $lBlue;
    }

    protected static function degradeToAnsi(int $red, int $green, int $blue): int
    {
        return (int) (floor($red / 128) + (floor($green / 128) * 2) + (floor($blue / 128) * 4));
    }

}
