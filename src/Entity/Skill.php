<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\SkillRepository;
use App\State\SkillStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[ApiProperty(openapiContext: [
        'example' => 'Piloting'
    ])]
    #[Groups(['skill:read', 'skill:write', 'gcm:read'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: GroundCrewMember::class, mappedBy: 'skills')]
    #[ApiProperty(openapiContext: [
        'example' => [
            '/api/ground-crew-members/0bae7248-76a3-4603-94a8-b1c2d410b8a2',
        ]
    ])]
    #[Groups(['skill:read', 'skill:write'])]
    private Collection $groundCrewMembers;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'requiredSkills')]
    #[Groups(['skill:read', 'skill:write'])]
    private Collection $tasks;

    public function __construct()
    {
        parent::__construct();

        $this->groundCrewMembers = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return Collection<int, GroundCrewMember>
     */
    public function getGroundCrewMembers(): Collection
    {
        return $this->groundCrewMembers;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addGroundCrewMember(GroundCrewMember $groundCrewMember): static
    {
        if (!$this->groundCrewMembers->contains($groundCrewMember)) {
            $this->groundCrewMembers->add($groundCrewMember);
            $groundCrewMember->addSkill($this);
        }

        return $this;
    }

    public function removeGroundCrewMember(GroundCrewMember $groundCrewMember): static
    {
        $this->groundCrewMembers->removeElement($groundCrewMember);

        return $this;
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
}
