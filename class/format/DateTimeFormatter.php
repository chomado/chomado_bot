<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\format;

use chomado\bot\DateTime;

/**
 * 日時情報整形クラス
 */
class DateTimeFormatter
{
    /**
     * 日付をフォーマットして返す
     *
     * @return string 例: 今日 15/1/20 は第04週目の水曜日です。今年の5.2%が経過しました。
     */
    public static function formatDateTime(DateTime $datetime)
    {
        return sprintf(
            '今日 %1$s は第%2$d週目の%3$s曜日です。今年の%4$.1f%%が経過しました。',
            $datetime->format('Y/m/d'),
            $datetime->format('W'),
            $datetime->getWDayName(),
            $datetime->getDaysPassedPercent()
        );
    }
}
