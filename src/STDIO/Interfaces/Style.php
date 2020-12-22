<?php

namespace NGSOFT\STDIO\Interfaces;

interface Style {

    /**
     * Get Prefixed Style
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Get Suffixed Style
     * @return string
     */
    public function getSuffix(): string;
}
