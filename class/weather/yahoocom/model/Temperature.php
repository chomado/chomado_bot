<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot\weather\yahoocom\model;

use stdClass;
use Exception;
use bot\unit\TemperatureConverter;

/**
 * 温度を保持するクラス
 */
class Temperature
{
    /**
     * セルシウス度であらわした温度
     *
     * SIはケルビンなのでケルビンで保持した方がそれらしい理由はできるが、
     * Δ1K = Δ1C なので実用的な近似値として摂氏で保持する。
     * @var double
     */
    private $degreeCelsius;

    /**
     * コンストラクタ
     *
     * @param double $degree 摂氏度, 華氏度
     * @param string $unit "C" または "F"
     * @throws \Exception 単位が異常なとき投げる
     */
    public function __construct($degree, $unit)
    {
        switch(strtoupper($unit)) {
            case 'C':
                $this->degreeCelsius = (double)$degree;
                break;
            case 'F':
                $this->degreeCelsius = TemperatureConverter::convertFToC((double)$degree);
                break;
            default:
                throw new Exception('Unknown temperature unit was given');
        }
    }

    /**
     * マジックメソッド __toString()
     *
     * @return string セルシウス度に単に「℃」をつけたものを返す。精度は適当。
     */
    public function __toString()
    {
        return sprintf('%.1f℃', $this->getAsCelsius());
    }

    /**
     * 摂氏として温度を取得する
     *
     * 精度は入力の精度と単位によるので適当に処理する必要がある
     *
     * @return double 摂氏
     */
    public function getAsCelsius()
    {
        return $this->degreeCelsius;
    }

    /**
     * 華氏として温度を取得する
     *
     * 精度は入力の精度と単位によるので適当に処理する必要がある
     *
     * @return double 華氏
     */
    public function getAsFahrenheit()
    {
        return TemperatureConverter::convertCToF($this->degreeCelsius);
    }
}
