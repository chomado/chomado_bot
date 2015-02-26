<?php
namespace test;

use bot\DateTime as MyDateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 閏年判定
     * @dataProvider leapYearProvider
     */
    public function testIsLeapYear($year, $expected)
    {
        $timeZoneUTC = new \DateTimeZone('UTC');
        $timestamp = gmmktime(0, 0, 0, 6, 1, $year);
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->isLeapYear());
    }

    /**
     * 年間日数
     * @dataProvider leapYearProvider
     */
    public function testDaysInYear($year, $isLeap)
    {
        $expected = $isLeap ? 366 : 365;
        $timeZoneUTC = new \DateTimeZone('UTC');
        $timestamp = gmmktime(0, 0, 0, 6, 1, $year);
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->getDaysInYear());
    }

    public function leapYearProvider()
    {
        // [ [ year, bool ], ... ]
        return [
            [ 2004, true  ],    // 4で割り切れる年は閏年
            [ 2001, false ],    // 4で割り切れない年は平年
            [ 2100, false ],    // 4で割り切れるにもかかわらず100で割り切れる年は平年
            [ 2000, true  ],    // 100で割り切れるにもかかわらず400で割り切れる年は閏年
        ];
    }

    /**
     * 経過日数
     * @dataProvider dayOfYearProvider
     */
    public function testGetDayOfYear($timestamp, $expected)
    {
        $timeZoneUTC = new \DateTimeZone('UTC');
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->getDayOfYear());
    }

    public function dayOfYearProvider()
    {
        // [ [ timestamp, days ], ... ]
        return [
            [ gmmktime(0, 0, 0, 1, 1, 2001),   0 ], // 2001-01-01
            [ gmmktime(0, 0, 0, 1, 2, 2001),   1 ], // 2001-01-02
            [ gmmktime(0, 0, 0, 12, 31, 2001), 364 ], // 2001-12-31
            [ gmmktime(0, 0, 0, 12, 31, 2004), 365 ], // 2004-12-31
        ];
    }

    /**
     * 経過秒数
     * @dataProvider elapsedSecondsOfDayProvider
     */
    public function testGetElapsedSecondsOfDay($timestamp, $expected)
    {
        $timeZoneUTC = new \DateTimeZone('UTC');
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->getElapsedSecondsOfDay());
    }

    public function elapsedSecondsOfDayProvider()
    {
        // [ [ timestamp, seconds ], ... ]
        return [
            [ gmmktime(0, 0, 0, 1, 1, 2001),     0 ], // 00:00:00
            [ gmmktime(0, 0, 1, 1, 1, 2001),     1 ], // 00:00:01
            [ gmmktime(0, 1, 0, 1, 1, 2001),    60 ], // 00:01:00
            [ gmmktime(1, 0, 0, 1, 1, 2001),  3600 ], // 01:00:00
            [ gmmktime(23, 59, 59, 1, 1, 2001), 86399 ], // 23:59:59
            [ gmmktime(23, 59, 59, 12, 31, 2004), 86399 ], // 23:59:59
        ];
    }

    /**
     * 経過%
     * @dataProvider daysPassedpercentProvider
     */
    public function testGetDaysPassedPercent($timestamp, $expected)
    {
        $allow = (1 / (365 * 86400) * 100) / 2; // 平年 0.5 秒分の誤差まで許容

        $timeZoneUTC = new \DateTimeZone('UTC');
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->getDaysPassedPercent(), '', $allow);
    }

    public function daysPassedpercentProvider()
    {
        // [ [ timestamp, percent ], ... ]
        return [
            [ gmmktime(0, 0, 0, 1, 1, 2001),               0 ], // 2001-01-01 00:00:00
            [ gmmktime(0, 0, 0, 1, 2, 2001),   1 / 365 * 100 ], // 2001-01-02 00:00:00
            [ gmmktime(0, 0, 0, 12, 31, 2001), 364 / 365 * 100 ], // 2001-12-31 00:00:00
            [ gmmktime(0, 0, 0, 12, 31, 2004), 365 / 366 * 100 ], // 2004-12-31 00:00:00
            
            [ gmmktime(0, 0, 1, 1, 1, 2001), 1 / (86400 * 365) * 100 ],                    // 2001-01-01 00:00:01
            [ gmmktime(23, 59, 59, 12, 31, 2001), (86400 * 365 - 1) / (86400 * 365) * 100 ],    // 2001-12-31 23:59:59
            [ gmmktime(23, 59, 59, 12, 31, 2004), (86400 * 366 - 1) / (86400 * 366) * 100 ],    // 2004-12-31 23:59:59
        ];
    }

    /**
     * 曜日
     * @dataProvider wdayNameProvider
     */
    public function testGetWDayName($timestamp, $expected)
    {
        $timeZoneUTC = new \DateTimeZone('UTC');
        $target = new MyDateTime("@{$timestamp}", $timeZoneUTC);
        $this->assertEquals($expected, $target->getWDayName());
    }

    public function wdayNameProvider()
    {
        // [ [ timestamp, wday ], ... ]
        return [
            [ gmmktime(0, 0, 0, 1, 1, 2006), '日' ],
            [ gmmktime(0, 0, 0, 1, 2, 2006), '月' ],
            [ gmmktime(0, 0, 0, 1, 3, 2006), '火' ],
            [ gmmktime(0, 0, 0, 1, 4, 2006), '水' ],
            [ gmmktime(0, 0, 0, 1, 5, 2006), '木' ],
            [ gmmktime(0, 0, 0, 1, 6, 2006), '金' ],
            [ gmmktime(0, 0, 0, 1, 7, 2006), '土' ],
        ];
    }
}
