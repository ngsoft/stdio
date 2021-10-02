<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress;

use Countable;
use NGSOFT\{
    STDIO, STDIO\Styles\Style
};
use Stringable;

abstract class ProgressElement implements Countable, Stringable {

    /** @var int */
    protected $current = 0;

    /** @var int */
    protected $total = 100;

    /** @var bool */
    protected $visible = true;

    /** @var ?Style */
    protected $style;

    /** @var STDIO */
    protected $stdio;

    ////////////////////////////   Abstract   ////////////////////////////

    /**
     * Build the element
     */
    abstract protected function build(): string;

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /**
     * @param STDIO $stdio
     * @param int $total
     * @param Style $style
     */
    public function __construct(
            STDIO $stdio,
            int $total
    ) {
        $this->stdio = $stdio;
        $this->total = $total;
        $this->style = $style;
    }

    /**
     * Checks if complete
     *
     * @return bool
     */
    public function getComplete(): bool {
        return $this->current == $this->total;
    }

    /**
     * Get Percentage Done
     * @return int
     */
    public function getPercent(): int {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $percent;
    }

    /**
     * Current position
     * @return int
     */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     * Total
     * @return int
     */
    public function getTotal(): int {
        return $this->total;
    }

    /**
     * Set Element Color
     *
     * @param string $color
     * @return $this
     */
    public function setColor(string $color) {
        $styles = $this->stdio->getStyles();
        $this->style = $styles[$color] ?? null;
        return $this;
    }

    /**
     * Render ?
     * @param bool $visible
     * @return static
     */
    public function setVisible(bool $visible) {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Set Current Value
     * @param int $current
     * @return static
     */
    public function setCurrent(int $current) {
        $this->current = max(0, min($current, $this->total));
        return $this;
    }

    /**
     * Set Total
     * @param int $total
     * @return static
     */
    public function setTotal(int $total) {
        $this->total = $total;
        return $this;
    }

    /**
     * Set Style
     * @param Style $style
     * @return static
     */
    public function setStyle(Style $style) {
        $this->style = $style;
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Increments the counter
     * @param int $value value to add
     * @return static
     */
    public function increment(int $value = 1) {
        $current = $this->current;
        $current += $value;
        $this->setCurrent($current);
        return $this;
    }

    /**
     * Decrements the Counter
     * @param int $value
     * @return $this
     */
    public function decrement(int $value = 1) {
        $current = $$this->current;
        $current -= $value;
        $this->setCurrent($value);
        return $this;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function count() {
        return $this->current;
    }

    /** {@inheritdoc} */
    public function __toString() {
        return $this->build();
    }

}