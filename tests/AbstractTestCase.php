<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ?Response $response;
    protected ?string $method;
    protected ?string $uri;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $this->response = null;
        $this->method = null;
        $this->uri = null;

        if (!$this->entityManager->getConnection()->isConnected()) {
            $this->entityManager->getConnection()->connect();
        }
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        $this->entityManager->getConnection()->close();
    }

    protected function request(
        ?string $method = null,
        ?string $uri = null,
        array $parameters = [],
        array $body = [],
        array $headers = []
    ): Response {
        $content = json_encode($body);

        $headers['Content-Length'] = mb_strlen($content, '8bit');
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';

        $this->client->request(
            $method ?? $this->method,
            $uri ?? $this->uri,
            $parameters,
            [],
            $this->translateHeadersToServerVars($headers),
            $content
        );
        $this->response = $this->client->getResponse();

        return $this->client->getResponse();
    }

    public static function assertResponseStructure(array $structure, string $message = ''): void
    {
        self::assertThatForResponse(new ResponseStructureMatch($structure), $message);
    }

    private function translateHeadersToServerVars(array $headers): array
    {
        $server = [];
        foreach ($headers as $header => $value) {
            $header = strtr(strtoupper($header), '-', '_');

            if ($header !== 'CONTENT_TYPE') {
                $header = 'HTTP_' . $header;
            }

            $server[$header] = $value;
        }

        return $server;
    }
}
