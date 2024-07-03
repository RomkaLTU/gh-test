<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\GroundCrewMemberRepository;
use App\State\GroundCrewMemberStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroundCrewMemberRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/ground-crew-members/{uuid}'),
        new Post(
            uriTemplate: '/ground-crew-members',
            processor: GroundCrewMemberStateProcessor::class,
        )
    ],
    normalizationContext: ['groups' => ['entity:read', 'gcm:read']],
    denormalizationContext: ['groups' => ['gcm:write']],
)]
class GroundCrewMember extends BaseEntity
{
    #[ORM\Column(length: 255)]
    #[Groups(['gcm:read', 'gcm:write'])]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
