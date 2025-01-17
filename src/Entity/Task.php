<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Enum\TaskStatusEnum;
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

    #[ORM\Column(type: 'string', enumType: TaskStatusEnum::class)]
    #[Groups(['task:read', 'task:write'])]
    private TaskStatusEnum $status;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_required_skills')]
    #[ApiProperty(openapiContext: [
        'example' => [
            '/api/skills/d97fd14b-0d71-4565-8d28-e62a34b5b0d2',
        ]
    ])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $requiredSkills;

    #[ORM\ManyToMany(targetEntity: Certification::class, inversedBy: 'tasks')]
    #[ORM\JoinTable(name: 'task_required_certifications')]
    #[ApiProperty(openapiContext: [
        'example' => [
            '/api/certificates/b1eaa418-ebfc-4ce5-ba83-fc263ec997dd',
        ]
    ])]
    #[Groups(['task:read', 'task:write'])]
    private Collection $requiredCertifications;

    #[ORM\ManyToOne(targetEntity: Flight::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(openapiContext: [
        'example' => '/api/flights/f6c0c7f8-4082-47e9-9cdc-ef7573f6702b',
    ])]
    #[Groups(['task:read', 'task:write'])]
    private ?Flight $flight = null;

    #[ORM\ManyToOne(targetEntity: GroundCrewMember::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(openapiContext: [
        'example' => '/api/ground-crew-members/8da044b2-1cd0-4ad0-87f2-832fbefa914c',
    ])]
    #[Groups(['task:read', 'task:write'])]
    private ?GroundCrewMember $assignedTo = null;

    public function __construct()
    {
        parent::__construct();

        $this->requiredSkills = new ArrayCollection();
        $this->requiredCertifications = new ArrayCollection();
        $this->status = TaskStatusEnum::PENDING;
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

    public function getRequiredCertifications(): Collection
    {
        return $this->requiredCertifications;
    }

    public function addRequiredCertification(Certification $certification): static
    {
        if (!$this->requiredCertifications->contains($certification)) {
            $this->requiredCertifications->add($certification);
        }

        return $this;
    }

    #[Groups(['task:write'])]
    public function setRequiredCertifications(array $certificationIris): void
    {
        $this->requiredCertifications = new ArrayCollection();

        foreach ($certificationIris as $iri) {
            $this->requiredCertifications->add($iri);
        }
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): static
    {
        $this->flight = $flight;

        return $this;
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TaskStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAssignedTo(): ?GroundCrewMember
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?GroundCrewMember $groundCrewMember): static
    {
        $this->assignedTo = $groundCrewMember;

        return $this;
    }
}
