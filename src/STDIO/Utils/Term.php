<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

final class Term
{

    public readonly bool $tty;
    public readonly bool $colors;

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
        $height = 24;
        if (Utils::isWindows()) {
            if (Utils::supportsPowershell()) {
                $width = trim(shell_exec('powershell.exe $Host.UI.RawUI.WindowSize.Width;'));
                $height = trim(shell_exec('powershell.exe $Host.UI.RawUI.WindowSize.Height'));
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

    /**
     * Get cursor position
     * @return int[] list($top,$left)
     */
    public function getCursorPosition(): array
    {
        static $input;
        $input ??= fopen('php://stdin', 'r+');
        $top = $left = 1;

        if (Utils::supportsPowershell()) {
            $top = intval(trim(shell_exec('powershell.exe $Host.UI.RawUI.CursorPosition.Y'))) + 1;
            $left = intval(trim(shell_exec('powershell.exe $Host.UI.RawUI.CursorPosition.X'))) + 1;
        } elseif ($this->tty && Utils::supportsSTTY() && is_string($mode = shell_exec('stty -g'))) {
            shell_exec('stty -icanon -echo');
            @fwrite($input, "\x1b[6n");
            $code = fread($input, 1024);
            shell_exec(sprintf('stty %s', $mode));
            sscanf($code, "\x1b[%d;%dR", $top, $left);
        }

        return [(int) $top, (int) $left];
    }

    /**
     * Cursor top position
     */
    public function getTop(): int
    {
        return $this->getCursorPosition()[0];
    }

    /**
     * Cursor Left position
     */
    public function getLeft(): int
    {
        return $this->getCursorPosition()[1];
    }

}
