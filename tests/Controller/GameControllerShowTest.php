<?php

namespace App\Tests\Controller;

use App\Entity\Game\Game;
use App\Tests\AbstractTestCase;

class GameControllerShowTest extends AbstractTestCase
{
    protected Game $game;

    public function setUp(): void
    {
        parent::setUp();

        $repository = $this->entityManager->getRepository(Game::class);
        $this->game = $repository->findOneBy([]);

        $this->method = 'GET';
        $this->uri = '/games/' . $this->game->getId();
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
            'id',
            'name',
            'steam_id'
        ]);
    }

    public function testCorrectness(): void
    {
        $this->request();

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals($this->game->getId(), $response['id']);
        $this->assertEquals($this->game->getName(), $response['name']);
        $this->assertEquals($this->game->getSteamId(), $response['steam_id']);
    }
}
