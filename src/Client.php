<?php

namespace RabbitMQ\Api;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\RequestException;
use RabbitMQ\Api\Repository\AbstractRepository;

class Client
{

    /** @var ClientInterface  */
    private $client;

    /**
     * Client constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $configuration
     * @param ClientInterface $client
     * @return static
     */
    public static function fromConfiguration(array $configuration, ClientInterface $client = null)
    {
        $client = $client ?: new \Guzzle\Http\Client();

        if (!array_key_exists('base_url', $configuration)) {
            throw new \RuntimeException("Configuration must contain a 'base_url'.");
        }

        $client->setBaseUrl($configuration['base_url']);
        $client->setConfig($configuration);

        if (isset($configuration['credentials']) && !empty($configuration['credentials'])) {
            $client->setDefaultOption('auth', $configuration['credentials']);
        }

        return new static($client);
    }

    /**
     * @param $name
     * @param string $host
     * @return AbstractRepository
     */
    public function getRepository($name, $host = '/')
    {
        $name = str_replace(__NAMESPACE__ . '\\Model\\', '', $name);

        $repositoryClassName = __NAMESPACE__ . "\\Repository\\{$name}Repository";

        if (class_exists($repositoryClassName) && is_subclass_of($repositoryClassName, AbstractRepository::class)) {
            return new $repositoryClassName($this, $host);
        }

        throw new \RuntimeException("Repository with name '{$repositoryClassName}' not found.");
    }

    /**
     * @param $method
     * @param $uri
     * @param null $body
     * @return string|null
     */
    public function query($method, $uri, $body = null)
    {
        $headers = null;
        if (in_array($method, ['PUT', 'POST', 'DELETE'])) {
            $headers = [ 'Content-type' => 'application/json' ];
        }
        try {
            return $this->client->createRequest($method, $uri, $headers, $body)->send()->getBody(true);
        } catch (RequestException $exception) {
            return null;
        }
    }
}
