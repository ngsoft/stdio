<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\Tools,
    Throwable;

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
        return $result ??= self::isWindows() && ! empty(Utils::executeProcess('powershell.exe -?'));
    }

    public static function supportsSTTY(): bool
    {
        static $result;
        return $result ??= ! self::isWindows() && ! empty(Utils::executeProcess('stty'));
    }

}
