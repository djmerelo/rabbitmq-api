<?php

namespace RabbitMQ\Api\Tests\Repository;

use RabbitMQ\Api\Client;
use RabbitMQ\Api\Model\Exchange;
use RabbitMQ\Api\Repository\ExchangeRepository;

class ExchangeRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /** @var Client|\PHPUnit_Framework_MockObject_MockObject */
    private $client;

    /** @var ExchangeRepository */
    private $repository;

    /**
     */
    public function testNotEmptyExchange()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('GET', '/api/exchanges/%2F/')
            ->willReturn('[{"name":"","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}}]');

        $exchanges = $this->repository->findAll();
        $this->assertNotEmpty($exchanges);
        $this->assertInstanceOf(Exchange::class, $exchanges[0]);
    }

    /**
     */
    public function testCreateExchange()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('PUT', '/api/exchanges/%2F/hello')
            ->willReturn('');

        $this->assertTrue($this->repository->create('hello'));
    }

    /**
     */
    public function testGetExchange()
    {
        $this->client->expects($this->once())
            ->method('query')->with('GET', '/api/exchanges/%2F/hello')
            ->willReturn('{"name":"hello","vhost":"/","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}}');

        $exchange = $this->repository->find('hello');

        $this->assertInstanceOf(Exchange::class, $exchange);
    }

    /**
     */
    public function testDeleteExchange()
    {
        $this->client->expects($this->once())
            ->method('query')
            ->with('DELETE', '/api/exchanges/%2F/hello')
            ->willReturn(true);

        $this->assertTrue($this->repository->delete('hello'));
    }

    protected function setUp()
    {
        $this->client = $this->getMock(Client::class, [ 'query' ], [], '', false);
        $this->repository = new ExchangeRepository($this->client, '/');
    }
}
