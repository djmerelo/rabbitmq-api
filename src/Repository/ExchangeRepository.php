<?php

namespace RabbitMQ\Api\Repository;

use RabbitMQ\Api\Model\Exchange;

class ExchangeRepository extends AbstractRepository
{

    public function find($name)
    {
        $response = $this->client->query(self::GET, $this->buildUri($name));
        return $response ? $this->createExchange(json_decode($response, true)) : $response;
    }

    public function findAll()
    {
        $response = $this->client->query(self::GET, $this->buildUri());
        return array_map([$this, 'createExchange'], $response ? json_decode($response, true): []);
    }

    public function create($name, $type = 'direct', $delete = false, $durable = true, $internal = false)
    {
        $body = json_encode(['type' => $type, 'auto_delete' => $delete, 'durable' => $durable, 'internal' => $internal]);

        return $this->client->query(self::PUT, $this->buildUri($name), $body) !== null;
    }

    public function delete($name)
    {
        return $this->client->query(self::DELETE, $this->buildUri($name));
    }

    private function createExchange(array $exchangeInfo)
    {
        return new Exchange($exchangeInfo);
    }

    private function buildUri($name = null)
    {
        return sprintf('/api/exchanges/%s/%s', urlencode($this->virtualHost()), urlencode($name));
    }
}
