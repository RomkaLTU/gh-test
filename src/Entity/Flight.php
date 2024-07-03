<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Enum\FlightTypeEnum;
use App\Repository\FlightRepository;
use App\State\FlightStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/flights/{uuid}'),
        new Post(processor: FlightStateProcessor::class)
    ],
    normalizationContext: ['groups' => ['entity:read', 'flight:read']],
    denormalizationContext: ['groups' => ['flight:write']],
)]
class Flight extends BaseEntity
{
    #[ORM\Column(type: 'string', enumType: FlightTypeEnum::class)]
    #[Groups(['flight:read', 'flight:write'])]
    private FlightTypeEnum $type;

    #[ORM\Column(length: 255)]
    #[Groups(['flight:read', 'flight:write'])]
    private string $nr;

    public function getNr(): ?string
    {
        return $this->nr;
    }

    public function setNr(string $nr): static
    {
        $this->nr = $nr;

        return $this;
    }

    public function getType(): FlightTypeEnum
    {
        return $this->type;
    }

    public function setType(FlightTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }
}
