<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\{
    STDIO, STDIO\Outputs\Output
};
use function preg_exec;

final class Term
{

    public readonly bool $tty;
    public readonly bool $colors;
    protected ?Output $output = null;

    public function __construct()
    {
        $this->tty = Utils::supportsTTY();
        $this->colors = Utils::supportsColors();
    }

    /**
     * Get Terminal size
     * @return int[] list($width, $height)
     */
    public function getSize(): array
    {
        $width = 80;
        $height = 25;
        if (Utils::isWindows()) {
            if (Utils::supportsPowershell()) {
                $width = trim(shell_exec('powershell.exe $Host.UI.RawUI.WindowSize.Width;') ?? '80');
                $height = trim(shell_exec('powershell.exe $Host.UI.RawUI.WindowSize.Height') ?? '25');
            } elseif ($out = Utils::executeProcess('mode.com con /status')) {
                list($height, $width) = array_map(fn($arr) => $arr[1], preg_exec('#(\d+)#', $out, 2));
            }
        } elseif (Utils::supportsSTTY() && $out = Utils::executeProcess('stty size')) {
            list($height, $width) = array_map(fn($arr) => $arr[1], preg_exec('#(\d+)#', $out, 2));
        }
        return [(int) $width, (int) $height];
    }

    /**
     * Get terminal width
     */
    public function getWidth(): int
    {
        return $this->getSize()[0];
    }

    /**
     * Get terminal height
     */
    public function getHeight(): int
    {
        return $this->getSize()[1];
    }

    public function isCursorEnabled(): bool
    {
        static $result;

        return $result;
    }

    /**
     * Get cursor position
     * @param bool &$enabled true if position can be read
     * @return int[] list($top,$left)
     */
    public function getCursorPosition(&$enabled = null): array
    {

        static $canread;

        $canread ??= Utils::isCursorPosEnabled();

        $enabled = $canread;

        if ( ! $canread) {
            return [1, 1];
        }


        return $this->readCursorPosition(); ;
    }

    /**
     * @internal
     */
    public function readCursorPosition(): array
    {
        static $input;
        $input ??= fopen('php://stdin', 'r+');
        $top = $left = 1;

        if (Utils::supportsPowershell()) {
            $top = intval(trim(shell_exec('powershell.exe $Host.UI.RawUI.CursorPosition.Y') ?? '0')) + 1;
            $left = intval(trim(shell_exec('powershell.exe $Host.UI.RawUI.CursorPosition.X') ?? '0')) + 1;
        } elseif ($this->tty && Utils::supportsSTTY() && is_string($mode = shell_exec('stty -g'))) {

            shell_exec('stty -icanon -echo');
            @fwrite($input, "\x1b[6n");
            $code = fread($input, 1024);
            shell_exec(sprintf('stty %s', $mode));
            sscanf($code, "\x1b[%d;%dR", $top, $left);
        }

        return [(int) $left, (int) $top];
    }

    /**
     * Cursor top position
     */
    public function getTop(): int
    {
        return $this->getCursorPosition()[1];
    }

    /**
     * Cursor Left position
     */
    public function getLeft(): int
    {
        return $this->getCursorPosition()[0];
    }

    public function __debugInfo(): array
    {

        return [
            'colors' => $this->colors,
            'dimensions' => [
                'width' => $this->getWidth(),
                'height' => $this->getHeight(),
            ],
            'cursor' => [
                'left' => $this->getLeft(),
                'top' => $this->getTop(),
            ]
        ];
    }

}
