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
        $skillData = [
            'name' => 'Piloting',
        ];

        $skillResponse = $this->client->request('POST', '/api/skills', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $skillData,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $skillId = $skillResponse->toArray()['@id'];

        $data = [
            'name' => 'John Doe',
            'skills' => [$skillId],
        ];

        $response = $this->client->request('POST', '/api/ground-crew-members', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $data,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'name' => 'John Doe',
            'skills' => [
                ['@id' => $skillId],
            ],
        ]);

        return $response->toArray();
    }

    /**
     * @depends testCreateGroundCrewMember
     */
    public function testGroundCrewMemberHasSkill(array $data): void
    {
        $uuid = $data['uuid'];

        $response = $this->client->request('GET', '/api/ground-crew-members/' . $uuid);
        $responseData = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertArrayHasKey('skills', $responseData);
        $this->assertNotEmpty($responseData['skills']);
        $this->assertCount(1, $responseData['skills']);
        $this->assertEquals('Piloting', $responseData['skills'][0]['name']);
    }

    /**
     * @depends testCreateGroundCrewMember
     */
    public function testGetExistingGroundCrewMember(array $data): void
    {
        $uuid = $data['uuid'];

        $response = $this->client->request('GET', '/api/ground-crew-members/' . $uuid);
        $responseData = $response->toArray();

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context' => '/api/contexts/GroundCrewMember',
            '@id' => '/api/ground-crew-members/' . $uuid,
            '@type' => 'GroundCrewMember',
            'uuid' => $uuid,
            'name' => 'John Doe',
        ]);

        $this->assertArrayHasKey('skills', $responseData);
        $this->assertNotEmpty($responseData['skills']);
        $this->assertCount(1, $responseData['skills']);
        $this->assertEquals('Piloting', $responseData['skills'][0]['name']);
    }

    public function testGetNonExistentGroundCrewMember(): void
    {
        $nonExistentUuid = 'c022460d-2fcd-4ed1-a246-0d2fcdced147';
        $this->client->request('GET', '/api/ground-crew-members/' . $nonExistentUuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
