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

        $styles = [];
        if (isset($params['fg'])) {
            $fg = is_array($params['fg']) ? $params['fg'] : [$params['fg']];

            foreach ($fg as $colorName) {
                if ($style = $this->styles->getForegroundColor($colorName)) {
                    $styles[] = $style;
                }
            }
        }




        return $message;
    }

}
