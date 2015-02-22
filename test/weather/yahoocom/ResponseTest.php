<?php
namespace test\weather\yahoocom;
use bot\weather\yahoocom\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase {
    public function testLoadFromJson1() {
        $o = new Response(file_get_contents(__DIR__ . '/response.1.json'));
        $now = $o->getCondition();
        $this->assertInstanceOf('stdClass', $now);
        $this->assertInstanceOf('bot\weather\yahoocom\model\Weather', $now->weather);
        $this->assertInstanceOf('bot\weather\yahoocom\model\Temperature', $now->temp);
        $this->assertInstanceOf('bot\DateTime', $now->updatedAt);
        $this->assertEquals(33, $now->weather->getCode());
        $this->assertEquals('Fair', $now->weather->getEnglishText());
        $this->assertEquals(45, $now->temp->getAsFahrenheit());
        $this->assertEquals(1424861700, $now->updatedAt->getTimestamp());

        $tomorrow = $o->getTomorrow();
        $this->assertInstanceOf('stdClass', $tomorrow);
        $this->assertInstanceOf('bot\weather\yahoocom\model\Weather', $tomorrow->weather);
        $this->assertInstanceOf('bot\weather\yahoocom\model\Temperature', $tomorrow->tempHigh);
        $this->assertInstanceOf('bot\weather\yahoocom\model\Temperature', $tomorrow->tempLow);
        $this->assertEquals(32, $tomorrow->weather->getCode());
        $this->assertEquals('Sunny', $tomorrow->weather->getEnglishText());
        $this->assertEquals(67, $tomorrow->tempHigh->getAsFahrenheit());
        $this->assertEquals(46, $tomorrow->tempLow->getAsFahrenheit());

        $at = $o->getUpdatedAt();
        $this->assertInstanceOf('bot\DateTime', $at);
        $this->assertEquals(1424861700, $at->getTimestamp());
    }

    public function testLoadFromJson2() {
        $o = new Response(file_get_contents(__DIR__ . '/response.2.json'));
        $now = $o->getCondition();
        $this->assertEquals(27, $now->weather->getCode());
        $this->assertEquals('Mostly Cloudy', $now->weather->getEnglishText());
        $this->assertEquals(48, $now->temp->getAsFahrenheit());
        $this->assertEquals(1424867340, $now->updatedAt->getTimestamp());

        $tomorrow = $o->getTomorrow();
        $this->assertEquals(12, $tomorrow->weather->getCode());
        $this->assertEquals('Rain', $tomorrow->weather->getEnglishText());
        $this->assertEquals(48, $tomorrow->tempHigh->getAsFahrenheit());
        $this->assertEquals(42, $tomorrow->tempLow->getAsFahrenheit());

        $at = $o->getUpdatedAt();
        $this->assertEquals(1424867340, $at->getTimestamp());
    }

    public function testEmptyJson() {
        $this->setExpectedException('Exception');
        $o = new Response('{}');
    }

    public function testBrokenInput() {
        $this->setExpectedException('Exception');
        $o = new Response('abcdefg');
    }
}
