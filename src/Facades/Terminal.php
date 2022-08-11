<?php

declare(strict_types=1);

namespace NGSOFT\Facades;

use NGSOFT\{
    Container\ServiceProvider, Container\SimpleServiceProvider, STDIO\Utils\Term
};

/**
 * Terminal Facade
 */
class Terminal extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'Terminal';
    }

    protected static function getServiceProvider(): ServiceProvider
    {
        // please change this to declare custom services
        return new SimpleServiceProvider(static::getFacadeAccessor(), Term::class);
    }

    /**
     * Terminal color capability
     */
    public static function supportsColors(): bool
    {
        return static::getFacadeRoot()->colors;
    }

    /**
     * Get Terminal size
     * @return int[] list($width, $height)
     */
    public static function getSize(): array
    {
        return static::getFacadeRoot()->getSize();
    }

    /**
     * Get terminal width
     */
    public static function getWidth(): int
    {
        return static::getFacadeRoot()->getWidth();
    }

    /**
     * Get terminal height
     */
    public static function getHeight(): int
    {
        return static::getFacadeRoot()->getHeight();
    }

    /**
     * Get cursor position
     * @return int[] list($top,$left)
     */
    public static function getCursorPosition(): array
    {
        return static::getFacadeRoot()->getCursorPosition();
    }

    /**
     * Cursor top position
     */
    public static function getTop(): int
    {
        return static::getFacadeRoot()->getTop();
    }

    /**
     * Cursor Left position
     */
    public static function getLeft(): int
    {
        return static::getFacadeRoot()->getLeft();
    }

}
