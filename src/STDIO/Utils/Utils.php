<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

class Utils {

    public static function executeProcess(string $command): ?string {

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

}
