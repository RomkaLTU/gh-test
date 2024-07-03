<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\SkillRepository;
use App\State\SkillStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/skills/{uuid}'),
        new Post(processor: SkillStateProcessor::class),
    ],
    normalizationContext: ['groups' => ['entity:read', 'skill:read']],
    denormalizationContext: ['groups' => ['skill:write']],
)]
class Skill extends BaseEntity
{
    #[ORM\Column(length: 255)]
    #[Groups(['skill:read', 'skill:write'])]
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
