<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\FlightRepository;
use App\State\FlightStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/flights/{uuid}'),
        new Post(processor: FlightStateProcessor::class)
    ],
    normalizationContext: ['groups' => ['flight:read']],
    denormalizationContext: ['groups' => ['flight:write']],
)]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['flight:read'])]
    private Uuid $uuid;

    #[ORM\Column(length: 255)]
    #[Groups(['flight:read', 'flight:write'])]
    private string $nr;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getNr(): ?string
    {
        return $this->nr;
    }

    public function setNr(string $nr): static
    {
        $this->nr = $nr;

        return $this;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
