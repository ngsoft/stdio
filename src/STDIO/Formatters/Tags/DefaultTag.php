<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag;

/**
 * Used to manage tags without tag name but with params
 */
class DefaultTag extends Tag {

    public function format(string $message, array $params): string {

        if (!empty($params['tagName'])) return $message;

        $closing = $params['closing'];

        $formats = [];

        foreach ($params['fg'] ?? [] as $colorName) {
            if ($format = $this->styles->getForegroundColor($colorName)) $formats[] = $format;
            else throw new RuntimeException(sprintf('Invalid foreground color %s', $colorName));
        }

        foreach ($params['bg'] ?? [] as $colorName) {
            if ($format = $this->styles->getBackgroundColor($colorName)) $formats[] = $format;
            else throw new RuntimeException(sprintf('Invalid background color %s', $colorName));
        }

        foreach ($params['options'] as $formatName) {
            if ($format = $this->styles->getBackgroundColor($colorName)) $formats[] = $format;
            else throw new RuntimeException(sprintf('Invalid background color %s', $colorName));
        }




        var_dump($formats);

        return $message;
    }

}
