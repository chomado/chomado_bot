<?php
namespace chomado\bottest\weather\yahoocom\model;

use chomado\bot\weather\yahoocom\model\Weather;

class WeatherTest extends \PHPUnit_Framework_TestCase
{
    public function conditionProvider()
    {
        return [
            [   0, 'Tornado',       '竜巻'],
            [   2, 'Hurricane',     'ハリケーン'],
            [  20, 'Foggy',         '霧'],
            [3200, 'Not Available', '(サービス停止中)'],
            [9999, 'Unknown',       'Unknown (?9999?)'],
        ];
    }

    /**
     * @dataProvider conditionProvider
     */
    public function testWeather($code, $textEn, $textJa)
    {
        $cond = new Weather($code, $textEn);
        $this->assertEquals($code, $cond->getCode());
        $this->assertEquals($textEn, $cond->getEnglishText());
        $this->assertEquals($textJa, $cond->getJapaneseText());
        $this->assertNotEmpty($cond->__toString());
    }

    public function knownCodesProvider()
    {
        // return [[1], [2], ...]
        $ret = [];
        foreach (range(0, 47) as $i) {
            $ret[] = [$i];
        }
        $ret[] = [3200];
        return $ret;
    }

    /**
     * @dataProvider knownCodesProvider
     */
    public function testKnownCodes($code)
    {
        $cond = new Weather($code, 'test');
        $unknown = "?{$code}?";
        $this->assertTrue(strpos($cond->getJapaneseText(), $unknown) === false);
    }
}
