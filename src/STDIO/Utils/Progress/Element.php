<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress;

use Countable,
    NGSOFT\STDIO\Styles\Style,
    Stringable;
use function mb_strlen;

class Element implements Countable, Stringable {

    /** @var int */
    private $length = 0;

    /** @var string */
    private $value = '';

    /** @var Style|null */
    private $style;

    /**
     * Change Element Value
     * @param string $value
     * @return static
     */
    public function setValue(string $value) {
        $this->value = $value;
        $this->length = mb_strlen($value);
        return $this;
    }

    /**
     * Set Element Style
     * @param Style $style
     * @return $this
     */
    public function setStyle(Style $style) {
        $this->style = $style;
        return $this;
    }

    /** @return string */
    public function getValue(): string {
        return $this->value;
    }

    /** {@inheritdoc} */
    public function count() {
        return $this->length;
    }

    /** {@inheritdoc} */
    public function __toString() {
        if (empty($this->value)) return '';
        return is_null($this->style) ? $this->value : $this->style->format($this->value);
    }

}
