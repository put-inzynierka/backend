<?php

namespace App\Tests\Controller;

use App\Tests\AbstractTestCase;

class UserControllerStoreTest extends AbstractTestCase
{
    protected $testBody = [
        'email' => 'test@mail.com',
        'password' => 'Password123',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->method = 'POST';
        $this->uri = '/users';
    }

    public function testStatusCode(): void
    {
        $this->request(body: $this->testBody);

        $this->assertResponseIsSuccessful();
    }

    public function testResponseStructure(): void
    {
        $this->request(body: $this->testBody);

        $this->assertResponseStructure([
            'id',
            'email',
            'profile',
        ]);
    }
}
