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
        for ($i = 0; $i < $count; $i++) {
            $this->output->write(self::ESCAPE . $char);
        }

        return $this;
    }

    /**
     * Move Cursor UP
     *
     * @param int $count
     * @return self
     */
    public function up(int $count = 1): self {
        return $this->render(self::CURSOR_SUFFIX_UP, $count);
    }

    /**
     * Move Cursor DOWN
     *
     * @param int $count
     * @return self
     */
    public function down(int $count = 1): self {
        return $this->render(self::CURSOR_SUFFIX_DOWN, $count);
    }

    /**
     * Move Cursor LEFT
     *
     * @param int $count
     * @return type
     */
    public function left(int $count = 1) {
        return $this->render(self::CURSOR_SUFFIX_LEFT, $count);
    }

    /**
     * Move Cursor RIGHT
     *
     * @param int $count
     * @return type
     */
    public function right(int $count = 1) {
        return $this->render(self::CURSOR_SUFFIX_RIGHT, $count);
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
