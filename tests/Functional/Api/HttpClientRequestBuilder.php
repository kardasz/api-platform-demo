<?php

namespace App\Tests\Functional\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientRequestBuilder
{
    private Client $client;
    private string $method = '';
    private string $uri = '';
    private array $options = [];

    public function __construct(KernelBrowser $client)
    {
        $this->client = new Client($client);
    }

    public function withMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function withUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function withBody(string $body): self
    {
        if (is_file($body)) {
            $this->options['body'] = fopen($body, 'r');
        } elseif (is_file(__DIR__.'/Requests/'.$body)) {
            $this->options['body'] = file_get_contents(__DIR__.'/Requests/'.$body);
            $this->options['headers']['Content-Type'] = 'application/ld+json';
        } else {
            $this->options['body'] = $body;
        }

        return $this;
    }

    public function request(array $options = []): ResponseInterface
    {
        return $this->client->request(
            $this->method,
            $this->uri,
            array_replace_recursive($this->options, $options)
        );
    }
}
