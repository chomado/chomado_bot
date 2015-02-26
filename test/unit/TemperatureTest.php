<?php
namespace test\unit;

use bot\unit\TemperatureConverter;

class TemperatureConverterTest extends \PHPUnit_Framework_TestCase
{
    public function degreeProvider()
    {
        // [ [ degC, degF ], ... ] を返す
        return [
            [ -273.15, -459.67 ], // 絶対零度
            [ -30, -22 ],
            [ -20,  -4 ],
            [ -10,  14 ],
            [   0,  32 ],
            [  10,  50 ],
            [  20,  68 ],
            [  30,  86 ],
            [ 100, 212 ],
        ];
    }

    /**
     * @dataProvider degreeProvider
     */
    public function testFtoC($c, $f)
    {
        $actual = TemperatureConverter::convertFToC($f);
        $this->assertEquals($c, $actual, '', 0.01);
    }

    /**
     * @dataProvider degreeProvider
     */
    public function testCtoF($c, $f)
    {
        $actual = TemperatureConverter::convertCToF($c);
        $this->assertEquals($f, $actual, '', 0.01);
    }
}
