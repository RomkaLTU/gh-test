<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\GroundCrewMemberRepository;
use App\State\GroundCrewMemberStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    #[ApiProperty(openapiContext: [
        'example' => 'John Doe'
    ])]
    #[Groups(['gcm:read', 'gcm:write'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'groundCrewMembers')]
    #[ORM\JoinTable(name: 'ground_crew_member_skills')]
    #[ApiProperty(openapiContext: [
        'example' => [
            '/api/skills/d97fd14b-0d71-4565-8d28-e62a34b5b0d2',
        ]
    ])]
    #[Groups(['gcm:read', 'gcm:write'])]
    private Collection $skills;

    public function __construct()
    {
        parent::__construct();

        $this->skills = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }
}
