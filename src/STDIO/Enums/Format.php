<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

use NGSOFT\Enums\EnumTrait;

/**
 * @method static static RESET()
 * @method static static BOLD()
 * @method static static DIM()
 * @method static static ITALIC()
 * @method static static UNDERLINE()
 * @method static static INVERSE()
 * @method static static HIDDEN()
 * @method static static STRIKETROUGH()
 */
enum Format: int
{

    use EnumTrait;

    case RESET = 0;
    case BOLD = 1;
    case DIM = 2;
    case ITALIC = 3;
    case UNDERLINE = 4;
    case UNDERLINE2 = 21;
    case BLINK = 5;
    case BLINK_ALT = 6;
    case INVERSE = 7;
    case HIDDEN = 8;
    case STRIKETROUGH = 9;

    public function getUnsetValue(): int
    {
        $value = $this->getValue();
        switch ($value) {
            case 0:
                break;
            case 1:
            case 2:
                $value = 22;
                break;
            case 6:
                $value = 25;
                break;
            case 21:
                $value = 24;
                break;
            default:
                $value += 20;
        }

        return $value;
    }

    public function getFormatName(): string
    {
        return strtolower($this->getName());
    }

    public function getTag(): string
    {
        return strtolower($this->getName());
    }

    public function getTagAttribute(): string
    {
        return 'options';
    }

}
