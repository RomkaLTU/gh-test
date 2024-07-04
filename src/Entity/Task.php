<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\TaskRepository;
use App\State\TaskStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/tasks/{uuid}'),
        new Post(processor: TaskStateProcessor::class),
    ],
    normalizationContext: ['groups' => ['entity:read', 'task:read']],
    denormalizationContext: ['groups' => ['task:write']],
)]
class Task extends BaseEntity
{
    #[ORM\Column(length: 255)]
    #[ApiProperty(openapiContext: [
        'example' => 'Refuel'
    ])]
    #[Groups(['task:read', 'task:write'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_required_skills')]
    #[ApiProperty(openapiContext: [
        'example' => [
            '/api/skills/d97fd14b-0d71-4565-8d28-e62a34b5b0d2',
        ]
    ])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $requiredSkills;

    public function __construct()
    {
        parent::__construct();

        $this->requiredSkills = new ArrayCollection();
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
    public function getRequiredSkills(): Collection
    {
        return $this->requiredSkills;
    }

    public function addRequiredSkill(Skill $skill): static
    {
        if (!$this->requiredSkills->contains($skill)) {
            $this->requiredSkills->add($skill);
        }

        return $this;
    }

    /**
     * @param string[] $skillIris
     */
    #[Groups(['task:write'])]
    public function setRequiredSkills(array $skillIris): void
    {
        $this->requiredSkills = new ArrayCollection();

        foreach ($skillIris as $iri) {
            $this->requiredSkills->add($iri);
        }
    }
}
