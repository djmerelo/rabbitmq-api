<?php

namespace RabbitMQ\Api\Tests\Model;

use RabbitMQ\Api\Model\Exchange;

class ExchangeTest extends \PHPUnit_Framework_TestCase
{

    public function testName()
    {
        $exchange = new Exchange([ 'name' => 'hello' ]);
        $this->assertSame('hello', $exchange->name());
    }
}
