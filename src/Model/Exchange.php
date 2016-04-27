<?php

namespace RabbitMQ\Api\Model;

class Exchange
{
    private $info = [
        'name' => '',
        'vhost' => '',
        'type' => '',
        'durable' => true,
        'auto_delete' => false,
        'internal' => false,
    ];

    public function __construct($data)
    {
        $this->info = array_replace($this->info, $data);
    }

    public function name()
    {
        return $this->info['name'];
    }
}
