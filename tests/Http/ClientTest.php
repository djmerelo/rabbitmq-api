<?php

namespace RabbitMQ\Api\Tests\Http;

use Guzzle\Common\Event;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response;
use RabbitMQ\Api\Client;
use RabbitMQ\Api\Model\Queue;
use RabbitMQ\Api\Repository\QueueRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var Client */
    private $client;

    /** @var  \Guzzle\Http\Client|\PHPUnit_Framework_MockObject_MockObject */
    private $httpClient;


    public function testGetRepository()
    {
        $this->assertInstanceOf(QueueRepository::class, $this->client->getRepository('Queue'));
        $this->assertInstanceOf(QueueRepository::class, $this->client->getRepository(Queue::class));
    }

    public function testRepositoryNotFoundException()
    {
        $this->expectException(\RuntimeException::class);

        $this->expectExceptionMessage(
            "Repository with name 'RabbitMQ\\Api\\Repository\\NotFoundRepository' not found."
        );

        $this->client->getRepository('NotFound');
    }

    public function testCreateClientFromConfiguration()
    {
        $configuration = [ 'base_url' => 'http://192.168.1.1', 'credentials' => ['guest', 'guest'] ];
        $httpClient = new \Guzzle\Http\Client();

        $this->assertInstanceOf(Client::class, Client::fromConfiguration($configuration, $httpClient));
        $this->assertSame('http://192.168.1.1', $httpClient->getBaseUrl());
        $this->assertSame(['auth' => ['guest', 'guest']], $httpClient->getConfig()->get('request.options'));
    }

    public function testBaseUrlNotInConfiguration()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Configuration must contain a 'base_url'.");

        Client::fromConfiguration([]);
    }

    public function testQueryResponse()
    {
        $response = $this->getMock(Response::class, [ 'getBody' ], [], '', false);
        $response->method('getBody')->willReturn('[]');

        $this->httpClient->expects($this->once())->method('send')->willReturn($response);
        $this->assertSame('[]', $this->client->query('GET', '/'));
    }

    public function testJsonHeadersIncludedOnPut()
    {
        $response = $this->getMock(Response::class, [ 'getBody' ], [], '', false);
        $response->method('getBody')->willReturn('');

        /** @var Event $event */
        $event = null;

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener('client.create_request', function (Event $createEvent) use (&$event) {
            $event = $createEvent;
        });

        $this->httpClient->expects($this->once())->method('send')->willReturn($response);
        $this->httpClient->setEventDispatcher($eventDispatcher);
        $this->assertSame('', $this->client->query('PUT', '/'));
        $this->assertSame('application/json', $event->offsetGet('request')->getHeader('Content-type')->__toString());
    }

    public function testQueryReturnsNullOnBadRequest()
    {
        $this->httpClient->method('send')->willThrowException(new RequestException());

        $this->assertNull($this->client->query('GET', '/'));
    }

    /**
     * Sets the connection to rabbitMQ Management plugin.
     */
    protected function setUp()
    {
        $this->httpClient = $this->getMock(\Guzzle\Http\Client::class, [ 'send' ]);
        $this->client = new Client($this->httpClient);
    }
}
