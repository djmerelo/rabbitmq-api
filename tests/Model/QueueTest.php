<?php

namespace RabbitMQ\Api\Tests\Model;

use RabbitMQ\Api\Model\Queue;

class QueueTest extends \PHPUnit_Framework_TestCase
{

    public function testMemoryUsage()
    {
        $queue = new Queue([ 'memory' => '1024' ]);
        $this->assertSame(1024, $queue->usedMemory());
    }

    public function testActiveConsumers()
    {
        $queue = new Queue([ 'consumers' => '23' ]);
        $this->assertSame(23, $queue->consumers());
    }

    public function testReadyMessages()
    {
        $queue = new Queue([ 'messages_ready' => '3' ]);
        $this->assertSame(3, $queue->readyMessages());
    }

    public function testTotalMessages()
    {
        $queue = new Queue([ 'messages' => '6' ]);
        $this->assertSame(6, $queue->totalMessages());
    }
}
