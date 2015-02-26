<?php
namespace chomado\bottest\weather\yahoocom;

use chomado\bot\weather\yahoocom\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        ob_start();
        try {
            $ret = (new Client('tokyo'))->query();
            $this->assertInstanceOf('chomado\bot\weather\yahoocom\Response', $ret);
            ob_end_clean();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
