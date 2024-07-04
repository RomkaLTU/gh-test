<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Enum\FlightTypeEnum;
use App\Repository\FlightRepository;
use App\State\FlightStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'flight')]
    #[Groups(['flight:read'])]
    private Collection $tasks;

    #[ORM\Column(length: 255)]
    #[Groups(['flight:read', 'flight:write'])]
    private string $nr;

    public function __construct()
    {
        parent::__construct();

        $this->tasks = new ArrayCollection();
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

    public function getType(): FlightTypeEnum
    {
        return $this->type;
    }

    public function setType(FlightTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setFlight($this);
        }

        return $this;
    }
}
