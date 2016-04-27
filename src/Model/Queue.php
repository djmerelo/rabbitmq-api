<?php

namespace RabbitMQ\Api\Model;

class Queue
{

    private $info = [
        'consumers' => 0,
        'memory' => 0,
        'messages_ready' => 0,
        'messages_unacknowledged' => 0,
        'messages' => 0,
        'name' => '',
        'vhost' => '',
        'durable' => true,
        'auto_delete' => false,
        'node' => '',
    ];

    public function __construct(array $info)
    {
        $this->info = array_replace($this->info, $info);
    }

    public function totalMessages()
    {
        return (int)$this->info['messages'];
    }

    public function consumers()
    {
        return (int)$this->info['consumers'];
    }

    public function readyMessages()
    {
        return (int)$this->info['messages_ready'];
    }

    public function usedMemory()
    {
        return (int)$this->info['memory'];
    }
}
