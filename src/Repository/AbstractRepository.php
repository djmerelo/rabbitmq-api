<?php

namespace RabbitMQ\Api\Repository;

use RabbitMQ\Api\Client;

abstract class AbstractRepository
{

    const GET = 'GET';

    const POST = 'POST';

    const PUT = 'PUT';

    const DELETE = 'DELETE';

    protected $client;

    private $host;

    public function __construct(Client $client, $host)
    {
        $this->client = $client;
        $this->host = $host;
    }

    public abstract function find($name);
    public abstract function findAll();
    public abstract function create($name);
    public abstract function delete($name);

    protected function virtualHost()
    {
        return $this->host;
    }
}
