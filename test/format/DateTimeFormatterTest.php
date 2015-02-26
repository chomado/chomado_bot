<?php
namespace test\format;

use bot\format\DateTimeFormatter;

class DateTimeFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function formatProvider()
    {
        return [
            [ gmmktime(0, 0, 0, 2, 1, 2015), '2015/02/01', 5, '日',  8.5 ],
            [ gmmktime(0, 0, 0, 2, 8, 2015), '2015/02/08', 6, '日', 10.4 ],
        ];
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormatDateTime($timestamp, $date, $week, $wday, $percent)
    {
        $timeZoneUTC = new \DateTimeZone('UTC');
        $target = new \bot\DateTime("@{$timestamp}", $timeZoneUTC);
        $formatted = DateTimeFormatter::formatDateTime($target);
        
        $this->assertTrue(is_string($formatted));
        $this->assertEquals(1, preg_match('#(\d+/\d+/\d+).+?第(\d+)週目.+?(.)曜日.+?(\d+\.\d+)%#u', $formatted, $match));
        $this->assertEquals($date, $match[1]);
        $this->assertEquals($week, (int)$match[2]);
        $this->assertEquals($wday, $match[3]);
        $this->assertEquals($percent, (double)$match[4], '', 0.1);
    }
}
