<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress;

use Countable;
use NGSOFT\{
    STDIO, STDIO\Styles\Style
};
use Stringable;

class Element implements Countable, Stringable {

    /** @var int */
    private $length = 0;

    /** @var string */
    private $value = '';

    /** @var Style|null */
    private $style;

    /**
     * Change Element Value
     * @param string $value Formated value
     * @param int $length Length without format
     * @return $this
     */
    public function update(string $value, int $length) {
        $this->value = $value;
        $this->length = $length;
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
        return $this->value;
    }

}
