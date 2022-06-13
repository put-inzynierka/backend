<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestCase;

class GameControllerIndexTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->method = 'GET';
        $this->uri = '/games';
    }

    public function testStatusCode(): void
    {
        $this->request();

        $this->assertResponseIsSuccessful();
    }

    public function testResponseStructure(): void
    {
        $this->request();

        $this->assertResponseStructure([
            'total_count',
            'page',
            'page_count',
            'items' => [
                [
                    'steam_id',
                    'name',
                    'id',
                ],
            ],
        ]);
    }

    public function testPagination(): void
    {
        $this->request(parameters: [
            'page' => 1,
            'limit' => 2,
        ]);
        $secondInstance = json_decode($this->response->getContent(), true)['items'][1];

        $this->request(parameters: [
            'page' => 2,
            'limit' => 1,
        ]);

        $secondPageInstance = json_decode($this->response->getContent(), true)['items'][0];

        $this->assertEquals($secondInstance, $secondPageInstance);
    }
}
