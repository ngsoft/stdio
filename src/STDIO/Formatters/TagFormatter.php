<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Styles,
    Stringable;
use function mb_strlen,
             str_ends_with,
             str_starts_with;

class TagFormatter implements Formatter
{

    protected TagManager $manager;
    protected TagStack $tagStack;

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();

        $this->tagStack = new TagStack();

        $this->manager = new TagManager($this->styles);
    }

    protected function applyStyle(string $message, Tag $tag = null): string
    {
        if (is_null($tag)) {
            $tag = $this->tagStack->current();
        }

        return $tag->format($message);
    }

    /**
     * Escapes < and > special chars in given text.
     */
    public static function escape(string $message): string
    {
        return static::escapeTrailingBackslash(preg_replace('/([^\\\\]|^)([<>])/', '$1\\\\$2', $message));
    }

    public static function escapeTrailingBackslash(string $message): string
    {
        if (str_ends_with($message, '\\')) {
            $len = mb_strlen($message);
            $message = rtrim($message, '\\');
            $message = str_replace("\0", '', $message);
            $message .= str_repeat("\0", $len - mb_strlen($message));
        }
        return $message;
    }

    /** {@inheritdoc} */
    public function format(string|Stringable $message): string
    {

        $output = '';
        $offset = 0;

        if (preg_match_all('#<(([a-z\#](?:[^\\\\<>]*+ | \\\\.)*)|/([a-z\#][^<>]*+)?)>#ix', $message, $matches, PREG_OFFSET_CAPTURE)) {

            foreach ($matches[0] as $i => $match) {
                [$text, $pos] = $match;

                if (0 != $pos && '\\' == $message[$pos - 1]) {
                    continue;
                }

                $output .= $this->applyStyle(substr($message, $offset, $pos - $offset));

                $offset = $pos + strlen($text);

                $tag = $matches[1][$i][0];
                if ($closing = str_starts_with($tag, '/')) {
                    $tag = $matches[3][$i][0] ?? '';
                }

                $tagStyle = null;

                if ( ! empty($tag)) {


                    $tagStyle = $this->manager->findTagFromCode($tag);

                    if ($tagStyle->isSelfClosing()) {
                        $output .= $tagStyle->format('');
                        continue;
                    }
                }


                if ($closing) {
                    $this->tagStack->pop($tagStyle);
                    continue;
                }


                $tagStyle && $this->tagStack->push($tagStyle);
            }
        }

        $output .= $this->applyStyle(substr($message, $offset));

        return strtr($output, [
            "\0" => '\\',
            '\\<' => '<',
            '\\>' => '>',
            "\t" => '    ',
            '\t' => '    ',
            '\s' => ' ',
            '\n' => "\n",
            '\r' => "\r",
        ]);
    }

}
