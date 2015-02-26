<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\unit;

/**
 * 温度変換クラス
 */
class TemperatureConverter
{
    /**
     * 華氏から摂氏に変換する
     *
     * @param  float $degree_fahrenheit 華氏温度
     * @return float                    摂氏温度
     */
    public static function convertFToC($degreeFahrenheit)
    {
        return ((double)$degreeFahrenheit - 32) * 5 / 9;
    }

    /**
     * 摂氏から華氏に変換する
     *
     * @param  float $degreeCelsius 摂氏温度
     * @return float                華氏温度
     */
    public static function convertCToF($degreeCelsius)
    {
        return (double)$degreeCelsius * 9 / 5 + 32;
    }
}
