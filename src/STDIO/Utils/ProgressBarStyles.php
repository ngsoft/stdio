<?php

namespace NGSOFT\STDIO\Utils;

use InvalidArgumentException;
use NGSOFT\STDIO\{
    Styles, Styles\Style
};

class ProgressBarStyles {

    const DISPLAY_STATUS = 'status';
    const DISPLAY_LABEL = 'label';
    const DISPLAY_BAR = 'bar';
    const DISPLAY_PERCENT = 'percent';
    const LABEL_POSITION_LEFT = 'left';
    const LABEL_POSITION_RIGHT = 'right';
    const LABEL_POSITION_CENTER = 'center';
    const LABEL_NOPADDING = 'nopadding';

    /** @var Styles */
    private $styles;

    /** @var Style|null */
    private $barColor;

    /** @var Style|null */
    private $percentColor;

    /** @var Style|null */
    private $labelColor;

    /** @var Style|null */
    private $statusColor;

    /** @var string */
    private $labelPosition = self::LABEL_POSITION_CENTER;

    /** @var bool */
    private $displayBar = true;

    /** @var bool */
    private $displayPercent = true;

    /** @var bool */
    private $displayLabel = true;

    /** @var bool */
    private $displayStatus = true;

    /** @var string[] */
    private $displayOrder = [];

    public function __construct() {
        $this->styles = new Styles();
        $this->resetStyles();
    }

    /**
     * Reset Styles (Monochrome)
     */
    public function resetStyles() {
        $this->statusColor = $this->labelColor = $this->barColor = $this->percentColor = null;
        $this->displayOrder = [
            self::DISPLAY_LABEL,
            self::DISPLAY_STATUS,
            self::DISPLAY_BAR,
            self::DISPLAY_PERCENT,
        ];
    }

    ////////////////////////////   Setters/Getters  ////////////////////////////

    /**
     * Get Display Order
     * @return array
     */
    public function getDisplayOrder(): array {
        $order = [];
        foreach ($this->displayOrder as $item) {
            $prop = sprintf('display%s', ucfirst($item));
            if ($this->{$prop} == true) $order[] = $item;
        }
        return $order;
    }

    /**
     * Set the Display order
     * @param array $displayOrder
     * @return static
     */
    public function setDisplayOrder(array $displayOrder) {
        $this->displayOrder = $displayOrder;
        return $this;
    }

    public function getLabelPosition(): string {
        return $this->labelPosition;
    }

    public function setLabelPosition(string $labelPosition) {
        $accepted = [self::LABEL_POSITION_CENTER, self::LABEL_POSITION_LEFT, self::LABEL_POSITION_RIGHT, self::LABEL_NOPADDING];
        if (!in_array($labelPosition, $accepted)) {
            throw new InvalidArgumentException(sprintf("Invalid Label Position %s, accepted: %s.", $labelPosition, implode(', ', $accepted)));
        }
        $this->labelPosition = $labelPosition;
        return $this;
    }

    /**
     * Get Bar Color
     * @return Style|null
     */
    public function getBarColor(): ?Style {
        return $this->barColor;
    }

    /**
     * Get Percent Color
     * @return Style|null
     */
    public function getPercentColor(): ?Style {
        return $this->percentColor;
    }

    /**
     * Get Label Color
     * @return Style|null
     */
    public function getLabelColor(): ?Style {
        return $this->labelColor;
    }

    /**
     * Get Status Color
     * @return Style|null
     */
    public function getStatusColor(): ?Style {
        return $this->statusColor;
    }

    /**
     * Set Bar Color
     * @param string $barColor
     * @return static
     */
    public function setBarColor(string $barColor) {
        $this->barColor = $this->styles[$barColor] ?? null;
        return $this;
    }

    /**
     * Set Percent Color
     * @param string $percentColor
     * @return static
     */
    public function setPercentColor(string $percentColor) {
        $this->percentColor = $this->styles[$percentColor] ?? null;
        return $this;
    }

    /**
     * Set Label Color
     * @param string $labelColor
     * @return static
     */
    public function setLabelColor(string $labelColor) {

        $this->labelColor = $this->styles[$labelColor] ?? null;
        return $this;
    }

    /**
     * Set Status Color
     * @param string $statusColor
     * @return static
     */
    public function setStatusColor(string $statusColor) {
        $this->statusColor = $this->styles[$statusColor] ?? null;
        return $this;
    }

    /**
     * Display the Bar?
     * @param bool $displayBar
     * @return static
     */
    public function displayBar(bool $displayBar) {
        $this->displayBar = $displayBar;
        return $this;
    }

    /**
     * Display the Percentage?
     * @param bool $displayPercent
     * @return static
     */
    public function displayPercent(bool $displayPercent) {
        $this->displayPercent = $displayPercent;
        return $this;
    }

    /**
     * Display the Label?
     * @param bool $displayLabel
     * @return static
     */
    public function displayLabel(bool $displayLabel) {
        $this->displayLabel = $displayLabel;
        return $this;
    }

    /**
     * Display the Status?
     * @param bool $displayStatus
     * @return static
     */
    public function displayStatus(bool $displayStatus) {
        $this->displayStatus = $displayStatus;
        return $this;
    }

    /**
     * Set Styles to use
     * @param Styles $styles
     * @return static
     */
    public function setStyles(Styles $styles) {
        $this->styles = $styles;
        return $this;
    }

}
