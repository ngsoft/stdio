<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Helpers\Rect as HelperRect
};
use RuntimeException;

class Rect extends Tag
{

    protected bool $displayed = false;

    public function format(string $message): string
    {

        if ($this->displayed) {
            throw new RuntimeException('<rect> already displayed ! Please close tag in the same instruction it is open.');
        }


        try {



            $params = [];
            foreach (['length', 'padding', 'margin'] as $attribute) {

                $params[$attribute] = null;
                if (preg_match("#\d+#", $this->getFirstAttribute($attribute) ?? '', $matches)) {
                    $params[$attribute] = intval($matches[0]);
                }
            }



            $rect = new HelperRect($this->styles);

            is_int($params['length']) && $rect->setLength($params['length']);
            is_int($params['padding']) && $rect->setPadding($params['padding']);
            is_int($params['margin']) && $rect->setMargin($params['margin']);

            $style = $this->getStyle();
            if (count($style->getStyles())) {
                $rect->setStyle($style);
            }


            return $rect->format($message);
        } finally {
            $this->displayed = true;
        }
    }

}
