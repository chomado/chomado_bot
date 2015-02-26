<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;

/**
 * 日時クラス
 */
class DateTime extends \DateTime
{
    /** デフォルトタイムゾーンは日本標準時 */
    const DEFAULT_TIMEZONE = 'Asia/Tokyo';

    /**
     * コンストラクタ
     *
     * @param string        $time     日付/時刻を示す文字列
     * @param \DateTimeZone $timeZone $timeのタイムゾーンを示すオブジェクト。省略時はJST
     */
    public function __construct($time = 'now', \DateTimeZone $timeZone = null)
    {
        parent::__construct($time, $timeZone ? $timeZone : new \DateTimeZone(self::DEFAULT_TIMEZONE));
    }

    /**
     * 「今年」の日数を返す
     *
     * @return int 日数
     */
    public function getDaysInYear()
    {
        return 365 + ($this->isLeapYear() ? 1 : 0);
    }

    /**
     * 「今日」が「今年」の何日目か返す
     *
     * @return int
     */
    public function getDayOfYear()
    {
        return (int)$this->format('z');
    }

    /**
     * 「今」が「今日」の何秒目か返す
     *
     * @return float
     */
    public function getElapsedSecondsOfDay()
    {
        $hours      = (int)$this->format('G');
        $minutes    = (int)$this->format('i');
        $seconds    = (int)$this->format('s');
        $uSeconds   = (int)$this->format('u');
        return $hours * 3600 + $minutes * 60 + $seconds + $uSeconds / 1000000;
    }

    /**
     * 今年に入ってから何%経過したかを取得する
     *
     * @return float 百分率
     */
    public function getDaysPassedPercent()
    {
        $passed = $this->getDayOfYear() + $this->getElapsedSecondsOfDay() / 86400;
        return 100 * $passed / $this->getDaysInYear();
    }


    /**
     * 今年が閏年か判定して返す
     *
     * @return bool 閏年ならtrue
     */
    public function isLeapYear()
    {
        return !!$this->format('L');
    }

    /**
     * 曜日を取得する
     *
     * @return  string  曜日を表す文字列
     */
    public function getWDayName()
    {
        $japanese = [ '日', '月', '火', '水', '木', '金', '土' ];
        return $japanese[(int)$this->format('w')];
    }
}
