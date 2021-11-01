<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\STDIO\{
    Interfaces\Ansi, Interfaces\Output, Outputs\StreamOutput
};

class Cursor implements Ansi {

    /** @var Output */
    protected $output;

    public function __construct(Output $output = null) {
        $this->output = $output ?? new StreamOutput();
    }

    /**
     * @param string $char
     * @param int $count
     * @return static
     */
    protected function render(string $char, int $count): self {
        $count = max(1, $count);
        $this->output->write(str_repeat($char, $count));
        return $this;
    }

    /**
     * Move Cursor UP
     *
     * @param int $count
     * @return static
     */
    public function up(int $count = 1): self {
        return $this->render(self::CURSOR_UP, $count);
    }

    /**
     * Move Cursor DOWN
     *
     * @param int $count
     * @return static
     */
    public function down(int $count = 1): self {
        return $this->render(self::CURSOR_DOWN, $count);
    }

    /**
     * Move Cursor LEFT
     *
     * @param int $count
     * @return static
     */
    public function left(int $count = 1): self {
        return $this->render(self::CURSOR_LEFT, $count);
    }

    /**
     * Move Cursor RIGHT
     *
     * @param int $count
     * @return static
     */
    public function right(int $count = 1): self {
        return $this->render(self::CURSOR_RIGHT, $count);
    }

    /**
     * Clears the current line and moves the cursor to the start of the line
     *
     * @return static
     */
    public function clearLine(): self {
        $this->output->write("\r" . self::CLEAR_LINE);
        return $this;
    }

}
