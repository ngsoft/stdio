<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi, STDIO\Inputs\Input, STDIO\Outputs\Output
};
use RuntimeException;

/**
 * @property int $left
 * @property int $top
 * @property-read int $width
 * @property-read int $height
 * @property-read bool $enabled
 */
class Cursor
{

    /** @var Input */
    private $input;

    /** @var Output */
    private $output;

    public function __construct(
            Output $output = null,
            Input $input = null
    )
    {

        $this->output = $output ?? new Output();
        $this->input = $input ?? new Input();
    }

    /**
     * Move Cursor UP
     *
     * @param int $lines
     * @return static
     */
    public function moveUp(int $lines = 1): static
    {
        $lines = max(1, $lines);
        $this->output->write(sprintf(Ansi::CURSOR_UP, $lines));
        return $this;
    }

    /**
     * Move Cursor Down
     *
     * @param int $lines
     * @return static
     */
    public function moveDown(int $lines = 1): static
    {
        $lines = max(1, $lines);
        $this->output->write(sprintf(Ansi::CURSOR_DOWN, $lines));
        return $this;
    }

    /**
     * Move cursor Right
     * @param int $cols
     * @return static
     */
    public function moveRight(int $cols = 1): static
    {
        $cols = max(1, $cols);
        $this->output->write(sprintf(Ansi::CURSOR_RIGHT, $cols));
        return $this;
    }

    /**
     * Move cursor Left
     *
     * @param int $cols
     * @return static
     */
    public function moveLeft(int $cols = 1): static
    {
        $cols = max(1, $cols);
        $this->output->write(sprintf(Ansi::CURSOR_LEFT, $cols));
        return $this;
    }

    /**
     * Move to Column
     * @param int $col
     * @return static
     */
    public function moveToColumn(int $col): static
    {
        $col = max(1, $col);
        $this->output->write(sprintf(Ansi::CURSOR_COL, $col));
        return $this;
    }

    /**
     * Save cursor position
     * @return static
     */
    public function savePosition(): static
    {
        $this->output->write(Ansi::CURSOR_SAVE_POS);
        return $this;
    }

    /**
     * loads cursor position
     * @return static
     */
    public function restorePosition(): static
    {
        $this->output->write(Ansi::CURSOR_LOAD_POS);

        return $this;
    }

    /**
     * Clears current line
     * @return static
     */
    public function clearLine(): static
    {
        $this->output->write(Ansi::CLEAR_LINE);

        return $this;
    }

    /**
     * Clears from the cursor to the end of the line
     * @return static
     */
    public function clearEndLine(): static
    {
        $this->output->write(Ansi::CLEAR_END_LINE);

        return $this;
    }

    /**
     * Clears from the beginning of the line to the cursor
     * @return static
     */
    public function clearStartLine(): static
    {
        $this->output->write(Ansi::CLEAR_START_LINE);

        return $this;
    }

    /**
     * Clears from the top of the screen to the cursor
     * @return static
     */
    public function clearUp(): static
    {
        $this->output->write(Ansi::CLEAR_UP);

        return $this;
    }

    /**
     * Clears from the cursor to the bottom of the screen
     * @return static
     */
    public function clearDown(): static
    {
        $this->output->write(Ansi::CLEAR_DOWN);

        return $this;
    }

    /**
     * Clears output
     *
     * @return static
     */
    public function clearScreen(): static
    {
        $this->output->write(Ansi::CLEAR_SCREEN);

        return $this;
    }

    /**
     * Set Cursor Position
     *
     * @param int $x
     * @param int $y
     * @return static
     */
    public function setCurrentPosition(int $x, int $y): static
    {
        $x = max(1, $x);
        $y = max(1, $y);
        $this->output->write(sprintf(Ansi::CURSOR_POS, $y, $x));
        return $this;
    }

    /**
     * Returns Current Cursor position as [x,y] coordinates
     * @return int[]
     */
    public function getCurrentPosition(): array
    {
        return Terminal::getCursorPosition();
    }

    public function getWidth(): int
    {

        return Terminal::getWidth();
    }

    public function getHeight(): int
    {
        return Terminal::getHeight();
    }

    /**
     * Can cursor position be read?
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        Terminal::getCursorPosition($result);
        return (bool) $result;
    }

    /**
     * Cursor X
     * @return int
     */
    public function getLeft(): int
    {
        return $this->getCurrentPosition()[0];
    }

    /**
     * Cursor Y
     * @return int
     */
    public function getTop(): int
    {
        return $this->getCurrentPosition()[1];
    }

    public function __isset(string $name): bool
    {
        return method_exists($this, sprintf('get%s', ucfirst($name)));
    }

    public function __get(string $name): mixed
    {
        $method = sprintf('get%s', ucfirst($name));
        if ( ! method_exists($this, $method)) throw new RuntimeException("Invalid property $name.");
        return call_user_func([$this, $method]);
    }

    public function __set(string $name, mixed $value): void
    {


        if (in_array($name, ['left']) && is_int($value)) {
            list(, $y) = $this->getCurrentPosition();
            $this->setCurrentPosition($value, $y);
        } elseif (in_array($name, ['top']) && is_int($value)) {
            list($x) = $this->getCurrentPosition();
            $this->setCurrentPosition($x, $value);
        }
    }

    public function __unset(string $name): void
    {
        throw new RuntimeException(sprintf('Cannot unset %s::$%s', static::class, $name));
    }

    public function __debugInfo(): array
    {
        return [
            'top/left' => [$this->getTop(), $this->getLeft(),],
            'width/height' => [$this->getWidth(), $this->getHeight()],
        ];
    }

}
