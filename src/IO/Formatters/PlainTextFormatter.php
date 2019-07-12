<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Formatters;

class PlainTextFormatter extends Formatter {

    /** {@inheritdoc} */
    public function format(string $message): string {
        $message = strip_tags($message);
        //basic indentation using {:tab|space|br:} or {:        :}<= indentation persists
        //to do later wrap a class for custom tags
        $message = preg_replace_callback('/{:((?:\h+)?([\w*]+)?(?:\h+)?):}/m', function ($matches) {
            list(, $expr) = $matches;
            if (preg_match('/^(\w+)(?:(?:\*)(\d+))?$/', $expr, $matches)) {
                list(, $expr) = $matches;
                $repeat = 1;
                if (array_key_exists(2, $matches) and is_numeric($matches[2])) $repeat = intval($matches[2]);
                switch ($expr) {
                    case "br":
                        return str_repeat(PHP_EOL, $repeat);
                    //tabs \t don't display well with colors so we indent 4*space
                    case "tab":
                        $repeat *= 4;
                    case "space":
                        return str_repeat(" ", $repeat);
                }
            }
            // no matches : just strip the {::}
            return $expr;
        }, $message);

        $message = str_replace(['&gt;', '&lt;'], ['>', '<',], $message); //to display <> with TagFormatter enabled we must do that
        return strip_tags($message);
    }

}
