<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;

class SkillApiTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateSkill(): array
    {
        $data = [
            'name' => 'refueling',
        ];

        $response = $this->client->request('POST', '/api/skills', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $data,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'refueling',
        ]);

        return $response->toArray();
    }

    /**
     * @depends testCreateSkill
     */
    public function testGetExistingSkill(array $data)
    {
        $uuid = $data['uuid'];

        $this->client->request('GET', '/api/skills/' . $uuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context' => '/api/contexts/Skill',
            '@id' => '/api/skills/' . $uuid,
            '@type' => 'Skill',
            'uuid' => $uuid,
            'name' => 'refueling',
        ]);
    }

    public function testGetNonExistentSkill()
    {
        $nonExistentUuid = 'c022460d-2fcd-4ed1-a246-0d2fcdced147';
        $this->client->request('GET', '/api/skills/' . $nonExistentUuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
