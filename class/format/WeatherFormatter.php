<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace chomado\bot\format;

use DateTimeZone;
use chomado\bot\weather\yahoocom\Response as WeatherResponse;

/**
 * 天気情報整形クラス
 */
class WeatherFormatter
{
    /**
     * 天気情報を成形して取得する
     *
     * @param   \chomado\bot\weather\yahoocom\Response $data Y!Weatherレスポンスデータ
     * @param   string        $location 地名情報 e.g. 東京
     * @param   \DateTimeZone $timeZone 表示に利用するタイムゾーン
     * @return  string
     */
    public static function formatForWeatherTweet(WeatherResponse $weather, $location, DateTimeZone $timeZone)
    {
        $condition = $weather->getCondition();
        $tomorrow = $weather->getTomorrow();

        // 表示用タイムゾーンの設定
        $updatedAt = clone $condition->updatedAt;
        $updatedAt->setTimezone($timeZone);

        $format = "%1\$sの%2\$s現在の天気は、%3\$s(%4\$.1f℃)です。\n" .
                  "明日は%5\$sで、最高気温%6\$.1f℃、最低気温%7\$.1f℃です。";

        return sprintf(
            $format,
            $location,
            $updatedAt->format('H:i'),
            $condition->weather->getJapaneseText(),
            $condition->temp->getAsCelsius(),
            $tomorrow->weather->getJapaneseText(),
            $tomorrow->tempHigh->getAsCelsius(),
            $tomorrow->tempLow->getAsCelsius()
        );
    }
}
