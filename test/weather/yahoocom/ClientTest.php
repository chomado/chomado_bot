<?php
namespace test\weather\yahoocom;

use bot\weather\yahoocom\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        ob_start();
        try {
            $ret = (new Client('tokyo'))->query();
            $this->assertInstanceOf('bot\weather\yahoocom\Response', $ret);
            ob_end_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
