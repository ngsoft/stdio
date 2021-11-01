<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress;

use Countable;
use NGSOFT\{
    STDIO, STDIO\Styles, STDIO\Styles\Style
};
use Stringable;

abstract class ProgressElement implements Countable, Stringable {

    /** @var int */
    protected $current = 0;

    /** @var int */
    protected $total = 100;

    /** @var bool */
    protected $visible = true;

    /** @var STDIO */
    protected $stdio;

    /** @var Element */
    protected $element;

    /** @var Styles */
    protected $styles;

    ////////////////////////////   Abstract   ////////////////////////////

    /**
     * Build the element
     *
     * @param Element $element
     * @return Element
     */
    abstract protected function build(Element $element): Element;

    ////////////////////////////   Getters/Setters   ////////////////////////////

    /**
     * @param int $total
     * @param Styles $styles
     */
    public function __construct(
            int $total,
            Styles $styles
    ) {
        $this->styles = $styles;
        $this->total = $total;
        $this->element = new Element();
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
     * Get Percentage Done
     * @return int
     */
    public function getPercent(): int {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $percent;
    }

    /**
     * Set Element Color
     *
     * @param string|int $color
     * @return $this
     */
    public function setColor($color) {
        $styles = &$this->styles;
        if ($style = $styles[$color] ?? null) {
            $this->element->setStyle($style);
        }
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
        $this->current = $current;
        $this->element = $this->build($this->element);
        return $this;
    }

    /**
     * Set Total
     * @param int $total
     * @return static
     */
    public function setTotal(int $total) {
        $this->total = $total;
        $this->setCurrent(0);
        return $this;
    }

    /**
     * Set Style
     * @param Style $style
     * @return static
     */
    public function setStyle(Style $style) {
        $this->element->setStyle($style);
        return $this;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function count() {
        return $this->visible ? count($this->element) : 0;
    }

    /** {@inheritdoc} */
    public function __toString() {
        return $this->visible ? (string) $this->element : '';
    }

}
