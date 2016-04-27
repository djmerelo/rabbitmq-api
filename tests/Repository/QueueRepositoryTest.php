<?php

namespace RabbitMQ\Api\Tests\Repository;

use RabbitMQ\Api\Client;
use RabbitMQ\Api\Model\Queue;
use RabbitMQ\Api\Repository\QueueRepository;

class QueueRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var Client|\PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var QueueRepository */
    private $repository;

    /**
     * Tests that list queues returns an array of available queues.
     */
    public function testEmptyQueues()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('GET', '/api/queues/%2F/')
            ->willReturn('[]');

        $this->assertEmpty($this->repository->findAll());
    }

    /**
     */
    public function testNotEmptyQueues()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('GET', '/api/queues/%2F/')
            ->willReturn('[{"memory":4536,"messages_ready":0,"messages_unacknowledged":0,"messages":0,"consumers":0,"name":"hello","vhost":"/","durable":true,"auto_delete":false,"arguments":{},"node":""}]');

        $queues = $this->repository->findAll();
        $this->assertNotEmpty($queues);
        $this->assertInstanceOf(Queue::class, $queues[0]);
    }

    /**
     */
    public function testCreateQueue()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('PUT', '/api/queues/%2F/hello')
            ->willReturn('');

        $this->assertTrue($this->repository->create('hello'));
    }

    /**
     */
    public function testGetQueue()
    {
        $this->client->expects($this->once())
            ->method('query')->with('GET', '/api/queues/%2F/hello')
            ->willReturn('{"memory":4096,"consumers":0,"messages_ready":1,"messages":2}');

        $queue = $this->repository->find('hello');

        $this->assertInstanceOf(Queue::class, $queue);
    }

    /**
     */
    public function testDeleteQueue()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('DELETE', '/api/queues/%2F/hello')
            ->willReturn('');

        $this->assertTrue($this->repository->delete('hello'));
    }

    protected function setUp()
    {
        $this->client = $this->getMock(Client::class, [ 'query' ], [], '', false);
        $this->repository = new QueueRepository($this->client, '/');
    }
}
