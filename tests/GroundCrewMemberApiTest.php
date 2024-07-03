<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;

class GroundCrewMemberApiTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateGroundCrewMember(): array
    {
        $data = [
            'name' => 'John Doe',
        ];

        $response = $this->client->request('POST', '/api/ground-crew-members', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $data,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'John Doe',
        ]);

        return $response->toArray();
    }

    /**
     * @depends testCreateGroundCrewMember
     */
    public function testGetExistingGroundCrewMember(array $data): void
    {
        $uuid = $data['uuid'];

        $this->client->request('GET', '/api/ground-crew-members/' . $uuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context' => '/api/contexts/GroundCrewMember',
            '@id' => '/api/ground-crew-members/' . $uuid,
            '@type' => 'GroundCrewMember',
            'uuid' => $uuid,
            'name' => 'John Doe',
        ]);
    }

    public function testGetNonExistentGroundCrewMember(): void
    {
        $nonExistentUuid = 'c022460d-2fcd-4ed1-a246-0d2fcdced147';
        $this->client->request('GET', '/api/ground-crew-members/' . $nonExistentUuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
