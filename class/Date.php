<?php
/**
 * @author chomado <chomado@gmail.com>
 * @copyright 2015 by chomado <chomado@gmail.com>
 * @license https://github.com/chomado/chomado_bot/blob/master/LICENSE MIT
 */

namespace bot;

/**
 * 日付クラス
 */
class Date
{
    /** タイムゾーンは日本標準時 */
    const TIMEZONE = 'asia/tokyo';

    /** @var \DateTime 対象の時間 */
    private $time;
    
    /** コンストラクタ */
    public function __construct()
    {
        $this->time = new \DateTime('now', new \DateTimeZone(self::TIMEZONE));
    }

    /**
     * このクラスで管理している時刻を取得する 
     * 
     * @return \DateTime
     */
    public function GetTime()
    {
        return $this->time;
    }

    /**
     * 今年に入ってから何%経過したかを取得する
     *
     * @return float 百分率
     */
    private function DaysPassedPercent()
    {
        return $this->time->format('z') * 100 / (365 + $this->time->format('L'));
    }

    /**
     * 曜日を取得する
     *
     * @param   int     $weekNumber     曜日を示す数値(0～6)
     * @return  string                  曜日を表す文字列
     * @throws  \Exception              指定された数値が範囲外の場合に投げる
     */
    private function GetWeekName($weekNumber)
    {
        switch ($weekNumber) {
            case '0': return '日';
            case '1': return '月';
            case '2': return '火';
            case '3': return '水';
            case '4': return '木';
            case '5': return '金';
            case '6': return '土';
            default:  throw new \Exception('Unexpected weekday ' . $weekNumber);
        }
    }

    /**
     * 日付をフォーマットして返す
     *
     * @return string 例: 今日 15/1/20 は第04週目の水曜日です。今年の5.2%が経過しました。
     */
    public function GetDateMessage()
    {
        $message = sprintf('今日 %s は第%d週目の%s曜日です。今年の%.1f%%が経過しました。%s'
            , $this->time->format('Y/m/d')
            , $this->time->format('W')
            , $this->GetWeekName($this->time->format('w'))
            , $this->DaysPassedPercent()
            , PHP_EOL);
        return $message;
    }
}
