<?php

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\{
    Interfaces\Formatter, Styles
};

class Style implements Formatter {

    private $prefix = [];
    private $suffix = [];

    /** @return \NGSOFT\STDIO\Styles\Style */
    private function getClone(): Style {
        return clone $this;
    }

    /**
     * Get a clone with defined prefix
     * @param array $codes
     * @return \NGSOFT\STDIO\Styles\Style
     */
    public function withPrefix(array $codes): Style {
        $clone = $this->getClone();
        $clone->prefix = $codes;
        return $clone;
    }

    /**
     * Get a clone with defined suffix
     * @param array $codes
     * @return \NGSOFT\STDIO\Styles\Style
     */
    public function withSuffix(array $codes): Style {
        $clone = $this->getClone();
        $clone->suffix = $codes;
        return $clone;
    }

    /** {@inheritdoc} */
    public function format(string $message): string {
        $prefix = $suffix = '';
        if (count($this->prefix) > 0) $prefix = sprintf(Styles::ESCAPE . '%s' . Styles::STYLE_SUFFIX, implode(';', $this->prefix));
        if (count($this->suffix) > 0) $suffix = sprintf(Styles::ESCAPE . '%s' . Styles::STYLE_SUFFIX, implode(';', $this->prefix));
        return sprintf("%s%s%s", $prefix, $message, $suffix);
    }

}
