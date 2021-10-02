<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\STDIO\{
    Interfaces\Ansi, Interfaces\Output, Interfaces\Renderer, Outputs\StreamOutput, Terminal
};
use function mb_strlen;

class ProgressBar implements Renderer {

    // Style
    const ICON_PROGRESS = "▓";
    const ICON_DONE = "█";
    const ICON_LEFT = "░";
    const ICON_BORDER = "|";

    /** @var Terminal */
    protected $term;

    /** @var int */
    protected $total = 100;

    /** @var int */
    protected $current = 0;

    /** @var int */
    protected $percent = 0;

    /** @var string */
    protected $label = '';

    /** @var bool */
    protected $complete = false;

    /** @var callable[] */
    protected $onComplete = [];

    /** @var Output */
    protected $output;

    /** @var ProgressBarStyles */
    protected $progressBarStyles;

    public function __construct() {
        $this->term = new Terminal();
        $this->output = new StreamOutput();
        $this->progressBarStyles = new ProgressBarStyles();

        $this->progressBarStyles
                ->setBarColor('cyan')
                ->setPercentColor('white')
                ->setStatusColor('yellow')
                ->setLabelColor('green');
    }

    ////////////////////////////   Render  ////////////////////////////

    /**
     * Build the string to display into the terminal
     * @return string
     */
    protected function build(): string {

        $result = [
            sprintf("\r%s", Ansi::CLEAR_END_LINE)
        ];
        $components = $this->progressBarStyles->getDisplayOrder();
        $width = $this->term->width - 1;

        $reserved = 0;
        $barDisplayed = false;
        if (in_array(ProgressBarStyles::DISPLAY_BAR, $components)) {
            $reserved += 54;
            $barDisplayed = true;
        }
        if (in_array(ProgressBarStyles::DISPLAY_PERCENT, $components)) {
            $reserved += 6;
        }
        if (in_array(ProgressBarStyles::DISPLAY_STATUS, $components)) {
            $reserved += strlen('' . $this->total) * 2;
            $reserved += 4;
        }
        $available = $width - $reserved;

        foreach ($components as $component) {

            $method = sprintf('build%s', ucfirst($component));

            if ($data = $this->{$method}()) {

                if (
                        $component == ProgressBarStyles::DISPLAY_LABEL
                        and $barDisplayed
                ) {
                    //centers the label
                    $repeats = $available - 1;
                    $repeats -= $data['len'];
                    if ($data['len'] == 0) $repeats = 0;

                    switch ($this->progressBarStyles->getLabelPosition()) {
                        case ProgressBarStyles::LABEL_POSITION_LEFT:
                            $result[] = sprintf("%s%s ", $data['text'], $repeats > 0 ? str_repeat(' ', $repeats) : '');
                            break;
                        case ProgressBarStyles::LABEL_POSITION_RIGHT:
                            $result[] = sprintf("%s%s ", $repeats > 0 ? str_repeat(' ', $repeats) : '', $data['text']);
                            break;
                        case ProgressBarStyles::LABEL_POSITION_CENTER:
                            $padding_left = (int) ceil($repeats / 2);
                            $padding_right = $repeats - $padding_left;
                            $result[] = sprintf("%s%s%s ", $padding_left > 0 ? str_repeat(' ', $padding_left) : '', $data['text'], $padding_right > 0 ? str_repeat(' ', $padding_right) : '');
                            break;
                        default :
                            //no padding
                            $result[] = sprintf("%s ", $data['text']);
                    }
                } else $result[] = sprintf("%s ", $data['text']);
            }
        }

        return implode('', $result);
    }

    /**
     * Build the Label
     * @return array
     */
    protected function buildLabel(): array {
        $result = $this->label;

        $strlen = mb_strlen($result);
        if (
                !empty($result)
                and $this->term->hasColorSupport()
                and ($style = $this->progressBarStyles->getLabelColor())
        ) {
            $result = $style->format($result);
        }
        return [
            'len' => $strlen,
            'text' => $result,
        ];
    }

    /**
     * Build the Status Text
     * @return array
     */
    protected function buildStatus(): array {

        $tot = $this->total;
        $cur = $this->current;
        while (strlen("$cur") < strlen("$tot")) {
            $cur = sprintf('0%s', "$cur");
        }
        $result = sprintf('[%s/%s]', "$cur", "$tot");
        $strlen = mb_strlen($result);

        if (
                $this->term->hasColorSupport()
                and ($style = $this->progressBarStyles->getStatusColor())
        ) {
            $result = $style->format($result);
        }
        return [
            'len' => $strlen,
            'text' => $result,
        ];
    }

    /**
     * Build the Percentage Display
     * @return array
     */
    protected function buildPercent(): array {

        $percent = $this->getPercent();
        $result = '';
        $cnt = strlen("$percent");
        while ($cnt < 3) {
            $result .= " ";
            $cnt++;
        }
        $result .= "$percent";
        $result .= '%';
        $strlen = mb_strlen($result);
        if (
                $this->term->hasColorSupport()
                and ($style = $this->progressBarStyles->getPercentColor())
        ) {
            $result = $style->format($result);
        }
        return [
            'len' => $strlen,
            'text' => $result,
        ];
    }

    /**
     * Build the progress Bar
     * @return array
     */
    protected function buildBar(): array {
        $percent = $this->getPercent();
        $len_done = (int) floor($percent / 2);
        $len = 50 - $len_done;
        $progress = sprintf("%s%s%s%s", self::ICON_BORDER, $len_done > 0 ? str_repeat(self::ICON_DONE, $len_done) : '', $len > 0 ? str_repeat(self::ICON_PROGRESS, $len) : '', self::ICON_BORDER);
        $strlen = mb_strlen($progress);
        if (
                $this->term->hasColorSupport()
                and ($style = $this->progressBarStyles->getBarColor())
        ) {
            $progress = $style->format($progress);
        }
        return [
            'len' => $strlen,
            'text' => $progress,
        ];
    }

    /** {@inheritdoc} */
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

        if (is_string($label)) $this->setLabel($label);
        $this->setCurrent($current);
        return $this;
    }

    ////////////////////////////   Configure  ////////////////////////////

    /**
     * Callable to execute on Complete
     * @param callable $onComplete
     * @return static
     */
    public function onComplete(callable $onComplete) {
        $this->onComplete[] = $onComplete;
        return $this;
    }

    /**
     * Set Bar Color
     * @param string $barColor
     * @return static
     */
    public function setBarColor(string $barColor) {
        $this->progressBarStyles->setBarColor($barColor);

        return $this;
    }

    /**
     * Set Percent Color
     * @param string $percentColor
     * @return static
     */
    public function setPercentColor(string $percentColor) {
        $this->progressBarStyles->setPercentColor($percentColor);
        return $this;
    }

    /**
     * Set Label Color
     * @param string $labelColor
     * @return static
     */
    public function setLabelColor(string $labelColor) {
        $this->progressBarStyles->setLabelColor($labelColor);
        return $this;
    }

    /**
     * Set Status Color
     * @param string $statusColor
     * @return static
     */
    public function setStatusColor(string $statusColor) {
        $this->progressBarStyles->setStatusColor($statusColor);
        return $this;
    }

    ////////////////////////////   Setters/Getters  ////////////////////////////

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
                foreach ($this->onComplete as $callback) {
                    if (is_callable($callback)) call_user_func($callback);
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
     * Get Current Label
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * Set Current Label
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label) {
        $this->progressBarStyles->displayLabel(true);
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
     * Get Current Output
     * @return Output
     */
    public function getOutput(): Output {
        return $this->output;
    }

    /**
     * Set The Output to use
     * @param Output $output
     * @return static
     */
    public function setOutput(Output $output) {
        $this->output = $output;
        return $this;
    }

    /**
     * Get the Stylesheet
     * @return ProgressBarStyles
     */
    public function getProgressBarStyles(): ProgressBarStyles {
        return $this->progressBarStyles;
    }

}
