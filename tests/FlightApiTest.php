<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\FlightTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class FlightApiTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateFlight(): array
    {
        $flightData = [
            'nr' => 'FL123',
            'type' => FlightTypeEnum::ARRIVAL,
        ];

        $response = $this->client->request('POST', '/api/flights', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $flightData,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            'nr' => 'FL123',
        ]);

        return $response->toArray();
    }

    /**
     * @depends testCreateFlight
     */
    public function testGetExistingFlight(array $createdFlight): void
    {
        $uuid = $createdFlight['uuid'];

        $this->client->request('GET', '/api/flights/' . $uuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertJsonContains([
            '@context' => '/api/contexts/Flight',
            '@id' => '/api/flights/' . $uuid,
            '@type' => 'Flight',
            'uuid' => $uuid,
            'nr' => 'FL123',
        ]);
    }

    public function testGetNonExistentFlight(): void
    {
        $nonExistentUuid = 'c022460d-2fcd-4ed1-a246-0d2fcdced147';
        $this->client->request('GET', '/api/flights/' . $nonExistentUuid);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
