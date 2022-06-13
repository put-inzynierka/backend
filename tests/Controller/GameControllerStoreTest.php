<?php

namespace App\Tests\Controller;

use App\Entity\Game\Game;
use App\Tests\AbstractTestCase;

class GameControllerStoreTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->method = 'POST';
        $this->uri = '/games';
    }

    public function testStatusCode(): void
    {
        $this->request(body: [
            'name' => 'quake 3',
            'steam_id' => 123,
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testResponseStructure(): void
    {
        $this->request(body: [
            'name' => 'quake 3',
            'steam_id' => 123,
        ]);

        $this->assertResponseStructure([
            'id',
            'name',
            'steam_id',
        ]);
    }

    public function testResponseData(): void
    {
        $name = 'quake 3';
        $steamId = 123;

        $this->request(body: [
            'name' => $name,
            'steam_id' => $steamId,
        ]);

        $response = json_decode($this->response->getContent(), true);

        $this->assertSame($name, $response['name']);
        $this->assertSame($steamId, $response['steam_id']);
        $this->assertIsInt($response['id']);
    }

    public function testDatabasePresence(): void
    {
        $name = 'quake 3';
        $steamId = 123;

        $this->request(body: [
            'name' => $name,
            'steam_id' => $steamId,
        ]);

        $id = json_decode($this->response->getContent(), true)['id'];

        $repository = $this->entityManager->getRepository(Game::class);
        $game = $repository->find($id);

        $this->assertEquals($id, $game->getId());
        $this->assertEquals($name, $game->getName());
        $this->assertEquals($steamId, $game->isActive());
    }
}
