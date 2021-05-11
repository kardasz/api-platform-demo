<?php

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class RequestBuilder
{
    private KernelBrowser $client;
    private string $method = '';
    private string $uri = '';
    private array $parameters = [];
    private array $files = [];
    private array $server = [];
    private string $content = '';
    private bool $changeHistory = true;

    public function __construct(KernelBrowser $client)
    {
        $this->client = $client;
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

    public function withParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function withFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function withServer(array $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function withContent(string $content): self
    {
        if (is_file($content)) {
            $content = file_get_contents($content);
        } elseif (is_file(__DIR__.'/Requests/'.$content)) {
            $content = file_get_contents(__DIR__.'/Requests/'.$content);
        }

        $this->content = $content;

        return $this;
    }

    public function withFile(string $fieldName, UploadedFile $file): self
    {
        $this->files[$fieldName] = $file;

        return $this;
    }

    public function withChangeHistory(bool $changeHistory): self
    {
        $this->changeHistory = $changeHistory;

        return $this;
    }

    public function request(): Response
    {
        $this->client->request(
            $this->method,
            $this->uri,
            $this->parameters,
            $this->files,
            $this->server,
            $this->content,
            $this->changeHistory
        );

        return $this->client->getResponse();
    }
}
