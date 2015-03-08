<?php
namespace chomado\bottest\weather\yahoocom\model;

use chomado\bot\weather\yahoocom\model\Temperature;

class TemperatureTest extends \PHPUnit_Framework_TestCase
{
    public function degreeProvider()
    {
        return [
            [ -273.15, -459.67 ],
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
    public function testCelsiusInput($c, $f)
    {
        $temp = new Temperature($c, 'C');
        $this->assertEquals($c, $temp->getAsCelsius(), '', 0.01);
        $this->assertEquals($f, $temp->getAsFahrenheit(), '', 0.01);

        $toString = sprintf('%.1f℃', $c);
        $this->assertEquals($toString, $temp->__toString());
    }

    /**
     * @dataProvider degreeProvider
     */
    public function testFahrenheitInput($c, $f)
    {
        $temp = new Temperature($f, 'F');
        $this->assertEquals($c, $temp->getAsCelsius(), '', 0.01);
        $this->assertEquals($f, $temp->getAsFahrenheit(), '', 0.01);

        $toString = sprintf('%.1f℃', $c);
        $this->assertEquals($toString, $temp->__toString());
    }

    public function testUnknownUnit()
    {
        $this->setExpectedException('Exception');
        $temp = new Temperature(1, 'A');
    }
}
