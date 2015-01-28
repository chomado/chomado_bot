<?php
// 日付クラス
class Date
{
    // 現在時刻. タイムゾーンはJST指定
    private $time;
    
    public function __construct()
    {
        $this->time = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    }

    // 現在時刻のゲッター
    public function GetTime()
    {
        return $this->time;
    }

    // 今年に入ってから何%経過したか(%)が返る. 小数.
    private function DaysPassedPercent()
    {
        return $this->time->format('z') * 100 / (365 + $this->time->format('L'));
    }
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
            default:  return '???';
        }
    }

    // 今日 15/1/20 は第04週目の水曜日です。今年の5.2%が経過しました。
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