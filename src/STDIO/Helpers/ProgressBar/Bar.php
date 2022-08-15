<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

class Bar extends Element
{

    protected const BAR_PROGRESS = ['━', '█', '░'];
    protected const BAR_LEFT = ['╺', '░', '🟠'];
    protected const BAR_RIGHT = ['╸', '▓', '▌'];

    public function getLength(): int
    {
        return 20;
    }

    public function update(): void
    {


        static $pulse, $back, $complete, $finished, $premain;

        $pulse ??= $this->styles->createStyleFromString('yellow bold');
        $back ??= $this->styles->createStyleFromString('bg:black');

        $complete ??= $this->styles->createStyleFromString('green');

        $finished ??= $this->styles->createStyleFromString('green bold');

        $premain ??= $this->styles->createStyleFromString('magenta');

        if ($this->isComplete()) {
            $complete = $finished;
        }

        /**
         * "bar.back": "#3a3a3a",
          "bar.complete": "rgb(165,66,129)",
          "bar.finished": "rgb(114,156,31)",
          "bar.pulse": "rgb(165,66,129)",
          "general": "green",
          "nonimportant": "rgb(40,100,40)",
          "progress.data.speed": "red",
          "progress.description": "none",
          "progress.download": "green",
          "progress.filesize": "green",
          "progress.filesize.total": "green",
          "progress.percentage": "green",
          "progress.remaining": "rgb(40,100,40)",
         */
        $this->style ??= $this->styles['magenta'];
        $result = &$this->value;
        $result = '';

        $halves = $this->getPercent() * 2 * $this->getLength();

        $bar_count = (int) floor($halves / 2);
        $half_count = $halves % 2;

        if ($bar_count) {
            $result .= $complete->format(str_repeat(self::BAR_PROGRESS[2], $bar_count), true);
        }
        if ($half_count) {
            $result .= $complete->format(str_repeat(self::BAR_RIGHT[2], $half_count), true);
        }


        $remain = $this->getLength() - $bar_count - $half_count;

        if ($remain > 0) {

            if ( ! $half_count && $bar_count) {

                $result .= $pulse->format(self::BAR_LEFT[2], true);
                $remain --;
            }

            if ($remain > 0) {
                $result .= $premain->format(str_repeat(self::BAR_PROGRESS[2], $remain), true);
            }
        }


        $result = $back->format($result, true);

        $result .= "\r";
    }

}
