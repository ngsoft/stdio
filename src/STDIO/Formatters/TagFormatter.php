<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Formatters\Tags\NoTag, STDIO\Formatters\Tags\StyleTag,
    STDIO\Styles\Styles
};
use Stringable;
use function mb_strlen,
             str_ends_with,
             str_starts_with;

class TagFormatter implements Formatter
{

    protected TagManager $manager;

    protected const BUILTIN_TAGS = [NoTag::class, StyleTag::class];

    protected PrioritySet $tags;

    /** @var Tag[] */
    protected array $stack = [];

    /** @var Tag */
    protected Tag $defaultTag;

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();

        $this->manager = new TagManager($this->styles);

        $this->tags = new PrioritySet();

        foreach (self::BUILTIN_TAGS as $class) {
            $this->addTag(new $class($this->styles));
        }
    }

    /**
     * Add a custom tag to be managed
     */
    public function addTag(Tag $tag): void
    {

        foreach ($this->tags as $rtag) {
            if (get_class($rtag) === get_class($tag)) {
                return;
            }
        }
        $this->tags->add($tag, $tag->getPriority());
    }

    protected function getTagsFormat(array $attributes): string
    {

        $str = '';
        foreach ($this->tags as $tag) {
            $str .= $tag->getFormat($attributes);
        }
        return $str;
    }

    protected function getDefaultTag(): Tag
    {
        return $this->defaultTag ??= new NoTag($this->styles);
    }

    protected function getCurrentTag(): Tag
    {
        if (empty($this->stack)) {
            return $this->getDefaultTag();
        }
        return $this->stack[count($this->stack) - 1];
    }

    protected function push(Tag $tag): void
    {
        $this->stack[] = $tag;
    }

    protected function pop(?Tag $tag = null): Tag
    {

        if (empty($this->stack)) {
            return $this->getDefaultTag();
        }

        if ( ! $tag) {
            return array_pop($this->stack);
        }

        foreach (array_reverse($this->stack) as $index => $current) {
            if ($current->format('') === $tag->format('')) {
                $this->stack = array_slice($this->stack, 0, $index);
                return $current;
            }
        }
        throw new InvalidArgumentException(sprintf('Incorrect style closing tag "</%s>" found.', $tag->getStyle()));
    }

    protected function getTagForAttributes(array $attributes): Tag
    {

        foreach ($this->tags as $tag) {
            if ($tag->managesAttributes($attributes)) {
                return $tag->createFromAttributes($attributes, $this->styles);
            }
        }
        return $this->getDefaultTag();
    }

    protected function applyStyle(string $message, Tag $tag = null): string
    {
        if (is_null($tag)) {
            $tag = $this->getCurrentTag();
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

        if (preg_match_all('#<(([a-z](?:[^\\\\<>]*+ | \\\\.)*)|/([a-z][^<>]*+)?)>#ix', $message, $matches, PREG_OFFSET_CAPTURE)) {

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

                $style = null;

                if ( ! empty($tag)) {

                    $attributes = Tag::getTagAttributesFromCode($tag);

                    $tagStyle = $this->getTagForAttributes($attributes);

                    if ($tagStyle->isSelfClosing()) {
                        var_dump($tagStyle);
                        $output .= $tagStyle->format('');
                        continue;
                    }

                    // cache style for future use
                    if ($tagStyle::class === StyleTag::class && ! isset($this->styles[$tag])) {
                        $this->styles->addStyle(
                                $tagStyle->getStyle()
                        );
                    }
                }


                if ($closing) {
                    $this->pop($tagStyle);
                    continue;
                }


                $tagStyle && $this->push($tagStyle);
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
