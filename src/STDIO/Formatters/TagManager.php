<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\{
    DataStructure\PrioritySet, STDIO, STDIO\Styles\StyleList
};
use function implements_class,
             NGSOFT\Filesystem\require_all_once;

class TagManager
{

    protected PrioritySet $tags;

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
        $this->tags = new PrioritySet();
        $this->autoRegister();
    }

    public function register(Tag $tag): void
    {
        $class = get_class($tag);

        foreach ($this->tags as $rtag) {
            if (get_class($rtag) === $class) {
                return;
            }
        }

        $tag = clone $tag;
        $tag->setStyles($this->styles);

        $this->tags->add($tag, $tag->getPriority());
    }

    protected function autoRegister(): void
    {
        require_all_once(__DIR__ . '/Tags');

        foreach (implements_class(Tag::class) as $class) {
            $this->register(new $class($this->styles));
        }
    }

    /**
     * Find tag using code
     *
     * @phan-suppress PhanPluginAlwaysReturnMethod
     */
    public function findTagFromCode(string $code): Tag
    {
        /** @var Tag $tag */
        foreach ($this->tags as $tag) {

            if ($tag->managesCode($code)) {
                return $tag->createFromCode($code);
            }
        }
    }

    /**
     * Find tag using attributes
     *
     * @phan-suppress PhanPluginAlwaysReturnMethod
     */
    public function findTagFromAttributes(array $attributes): Tag
    {
        /** @var Tag $tag */
        foreach ($this->tags as $tag) {
            if ($tag->managesAttributes($attributes)) {
                return $tag->createFromAttributes($attributes);
            }
        }
    }

}
