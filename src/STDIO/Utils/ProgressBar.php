<?php

namespace NGSOFT\STDIO\Utils;

use NGSOFT\STDIO\{
    Interfaces\Output, Interfaces\Renderer, Outputs\StreamOutput, Styles, Terminal
};

class ProgressBar implements Renderer {

    /** @var Terminal */
    private $term;

    /** @var Styles */
    private $styles;

    /** @var string */
    private $color = 'cyan';

    /** @var int */
    private $total = 100;

    /** @var int */
    private $current = 0;

    /** @var int */
    private $percent = 0;

    /** @var string */
    private $label = '';

    /** @var bool */
    private $complete = false;

    /** @var callable|null */
    private $onComplete;

    /** @var Output */
    private $output;

    const ICON_DONE = "▓";
    const ICON_LEFT = "░";
    const ICON_BORDER = "|";

    public function __construct(int $total = 100, ?Output $output = null, ?callable $onComplete = null) {
        $this->term = new Terminal();
        $this->total = $total;
        if ($output instanceof Output) $this->output = $output;
        else $this->output = new StreamOutput();
        if (is_callable($onComplete)) $this->onComplete($onComplete);
    }

    /**
     * Build the progress bar
     * @return string
     */
    private function build(): string {

        $percent = $this->getPercent();
        $label = $this->getLabel();
        $progress_bar_len = 60;
        $len = 50;
        $len_done = (int) floor($percent / 2);
        $len -= $len_done;

        $width = $this->term->width - 1;

        $progress = sprintf("%s%s%s%s", self::ICON_BORDER, str_repeat(self::ICON_DONE, $len_done), str_repeat(self::ICON_LEFT, $len), self::ICON_BORDER);

        if (
                $this->term->hasColorSupport()
                and $this->styles instanceof Styles
                and isset($this->styles[$this->color])
        ) {
            $style = $this->styles[$this->color];
            $progress = $style->format($progress);
        }

        $progress .= " ";
        $cnt = strlen("$percent");
        while ($cnt < 3) {
            $progress .= " ";
            $cnt++;
        }
        $progress .= sprintf('%u', $percent);
        $progress .= '% ';

        $available = $width - $progress_bar_len;
        if ($available > 0) {
            if ($available > mb_strlen($label)) {
                $repeats = $available - mb_strlen($label) - 1;
                $label = $label . str_repeat(" ", $repeats);
            } else $label = str_repeat(" ", (int) floor($available / 2));
            $line = sprintf("\r%s%s%s", Styles::CLEAR_END_LINE, $label, $progress);
        } else $line = sprintf("\r%s%s", Styles::CLEAR_END_LINE, $progress);
        return $line;
    }

    public function render(Output $output) {
        $output->write($this->build());
    }

    /**
     * Increments the progress Bar
     * @param int $value
     * @param string|null $label
     * @return static
     */
    public function increment(int $value = 1, ?string $label = null) {
        $current = $this->current;
        $current += $value;
        $this->label = '';
        if (is_string($label)) $this->setLabel($label);
        $this->setCurrent($current);
        return $this;
    }

    /**
     * Set Max Number of items
     * @param int $total
     * @return static
     */
    public function setTotal(int $total) {
        $this->total = $total;
        return $this;
    }

    /**
     * Get Max Numbers of items
     * @return int
     */
    public function getTotal(): int {
        return $this->total;
    }

    /**
     * Set The current Value (also renders the progress bar)
     * @param int $current
     * @return static
     */
    public function setCurrent(int $current) {
        if (!$this->complete) {
            if ($current > $this->total) $current = $this->total;
            $this->current = $current;

            $this->render($this->output);
            if ($current >= $this->total) {
                $this->complete = true;
                $this->output->write("\n");
                if (is_callable($this->onComplete)) {
                    call_user_func($this->onComplete);
                }
            }
        }

        return $this;
    }

    /**
     * Get Current number of items
     * @return int
     */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     * Get string representation of current staus
     * @return string
     */
    public function getStatus(): string {
        $tot = $this->total;
        $cur = $this->current;
        while (strlen("$cur") < strlen("$tot")) {
            $cur = sprintf('0%s', "$cur");
        }
        return sprintf('[ %s / %s ]', "$cur", "$tot");
    }

    /**
     * Get Current Label
     * @return string
     */
    public function getLabel(): string {
        $label = $this->getStatus();
        if (!empty($this->label)) $label .= ' ' . $this->label;
        return $label;
    }

    /**
     * Set Current Label
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label) {
        $this->label = $label;
        return $this;
    }

    /**
     * Get Current Percentage
     * @return int
     */
    public function getPercent(): int {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $this->percent = $percent;
    }

    /**
     * Get Complete status
     * @return bool
     */
    public function isComplete(): bool {
        return $this->complete;
    }

    /**
     * Set Styles
     * @param Styles $styles
     * @return static
     */
    public function setStyles(Styles $styles) {
        $this->styles = $styles;
        return $this;
    }

    /**
     * Set Progress Bar Color
     * @param string $color
     * @return $this
     */
    public function setColor(string $color) {
        $this->color = $color;
        return $this;
    }

    /**
     * Callable to execute on Complete
     * @param callable $onComplete
     * @return $this
     */
    public function onComplete(callable $onComplete) {
        $this->onComplete = $onComplete;
        return $this;
    }

}
