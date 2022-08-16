<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Enums\Ansi;

final class Cursor
{

    public function __construct(
            protected ?Output $output = null
    )
    {
        $this->output ??= new Output();
    }

    protected function printf(string $format, int ...$arguments): static
    {
        $this->output->write(sprintf($format, ...$arguments));
        return $this;
    }

    ////////////////////////////   Move Cursor   ////////////////////////////

    /**
     * Move Cursor up
     */
    public function up(int $lines = 1)
    {
        return $this->printf(Ansi::CURSOR_UP, max(1, $lines));
    }

    /**
     * Move Cursor Down
     */
    public function down(int $lines = 1)
    {
        return $this->printf(Ansi::CURSOR_DOWN, max(1, $lines));
    }

    /**
     * Move cursor Right
     */
    public function right(int $cols = 1)
    {
        return $this->printf(Ansi::CURSOR_RIGHT, max(1, $cols));
    }

    /**
     * Move cursor Left
     */
    public function left(int $cols = 1)
    {
        return $this->printf(Ansi::CURSOR_LEFT, max(1, $cols));
    }

    /**
     * Moves cursor to beginning of the line n (default 1) lines down.
     */
    public function next(int $lines = 1)
    {
        return $this->printf(Ansi::CURSOR_NEXT_LINE, max(1, $lines));
    }

    /**
     * Moves cursor to beginning of the line n (default 1) lines up.
     */
    public function prev(int $lines = 1)
    {
        return $this->printf(Ansi::CURSOR_PREV_LINE, max(1, $lines));
    }

    /**
     * Moves the cursor to column n (default 1).
     */
    public function col(int $col = 1)
    {
        return $this->printf(Ansi::CURSOR_COL, max(1, max(1, $col)));
    }

    /**
     * Moves the cursor to position
     */
    public function setPosition(int $col, int $line)
    {

        return $this->printf(Ansi::CURSOR_POS, max(1, $line), max(1, $col));
    }

    /**
     * Save Current Cursor Position
     */
    public function save()
    {
        return $this->printf(Ansi::CURSOR_SAVE_POS);
    }

    /**
     * Restore Saved Cursor Position
     */
    public function load()
    {
        return $this->printf(Ansi::CURSOR_LOAD_POS);
    }

    ////////////////////////////   Erase Display   ////////////////////////////

    /**
     * Clears from cursor to the end of the screen
     */
    public function clearDown()
    {
        return $this->printf(Ansi::CLEAR_DOWN);
    }

    /**
     * Clear from cursor to beginning of the screen
     */
    public function clearUp()
    {
        return $this->printf(Ansi::CLEAR_UP);
    }

    /**
     * Clear entire screen
     */
    public function clear()
    {
        return $this->printf(Ansi::CLEAR_SCREEN);
    }

    /**
     * Clear from cursor to the end of the line
     */
    public function clearRight()
    {
        return $this->printf(Ansi::CLEAR_END_LINE);
    }

    /**
     * Clear from cursor to beginning of the line
     */
    public function clearLeft()
    {
        return $this->printf(Ansi::CLEAR_START_LINE);
    }

    /**
     * Clear entire line
     */
    public function clearLine()
    {
        return $this->printf(Ansi::CLEAR_LINE);
    }

    ////////////////////////////   Scroll   ////////////////////////////

    /**
     * Scroll whole page up by n (default 1) lines.
     */
    public function scrollUp(int $lines = 1)
    {
        return $this->printf(Ansi::SCROLL_UP, max(1, $lines));
    }

    /**
     * Scroll whole page down by n (default 1) lines.
     */
    public function scrollDown(int $lines = 1)
    {
        return $this->printf(Ansi::SCROLL_DOWN, max(1, $lines));
    }

    ////////////////////////////   Show/Hide   ////////////////////////////

    /**
     * Hides the cursor, from the VT220.
     */
    public function hide()
    {

        return $this->printf(Ansi::CURSOR_HIDE);
    }

    /**
     * Shows the cursor, from the VT220.
     */
    public function show()
    {

        return $this->printf(Ansi::CURSOR_SHOW);
    }

    ////////////////////////////   Read   ////////////////////////////

    /**
     * Get Cursor Position
     * @return int[] list($col,$line)
     */
    public function getPosition(): array
    {

    }

}
