<?php

namespace RabbitMQ\Api\Repository;

use RabbitMQ\Api\Model\Queue;

class QueueRepository extends AbstractRepository
{

    /**
     * @param $name
     * @return Queue|null
     */
    public function find($name)
    {
        $response = $this->client->query(self::GET, $this->buildUri($name));
        return $response ? $this->createQueue(json_decode($response, true)) : null;
    }

    /**
     * @return Queue[]
     */
    public function findAll()
    {
        $response = $this->client->query(self::GET, $this->buildUri());
        return array_map([$this, 'createQueue'], $response ? json_decode($response, true) : []);
    }

    public function create($name, $autoDelete = false, $durable = true)
    {
        $body = json_encode([ 'auto_delete' => $autoDelete, 'durable' => $durable ]);
        $response = $this->client->query(self::PUT, $this->buildUri($name), $body);
        return $response !== null;
    }

    public function delete($name)
    {
        return $this->client->query(self::DELETE, $this->buildUri($name)) !== null;
    }

    private function createQueue(array $queueInfo)
    {
        return new Queue($queueInfo);
    }

    private function buildUri($name = null)
    {
        return sprintf('/api/queues/%s/%s', urlencode($this->virtualHost()), urlencode($name));
    }
}
