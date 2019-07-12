<?php

namespace NGSOFT\Tools\IO\Styles;

class ResetStyle extends Style {

    public function __construct() {
        parent::__construct("reset");
    }

    /** {@inheritdoc} */
    public function withBackgroundColor(int $color) {
        return $this;
    }

    /** {@inheritdoc} */
    public function withColor(int $color) {
        return $this;
    }

    /** {@inheritdoc} */
    public function withName(string $name) {
        return $this;
    }

    /** {@inheritdoc} */
    public function withStyles(int ...$options) {
        return $this;
    }

    /** {@inheritdoc} */
    public function applyTo(string $message): string {
        return "\033[0m" . $message;
    }

}
