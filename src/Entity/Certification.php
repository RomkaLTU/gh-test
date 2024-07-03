<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\CertificationRepository;
use App\State\CertificateStateProcessor;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/certificates/{uuid}'),
        new Post(processor: CertificateStateProcessor::class),
    ],
    normalizationContext: ['groups' => ['entity:read', 'cert:read']],
    denormalizationContext: ['groups' => ['cert:write']],
)]
class Certification extends BaseEntity
{
    #[ORM\Column(length: 255)]
    #[ApiProperty(openapiContext: [
        'example' => 'Medical Certificate'
    ])]
    #[Groups(['cert:read', 'cert:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[ApiProperty(openapiContext: [
        'example' => '2024-12-31T23:59:59+00:00'
    ])]
    #[Groups(['cert:read', 'cert:write'])]
    private ?DateTimeImmutable $validity_date = null;

    #[ORM\ManyToMany(targetEntity: GroundCrewMember::class, mappedBy: 'certifications')]
    private Collection $groundCrewMembers;

    public function __construct()
    {
        parent::__construct();
        $this->groundCrewMembers = new ArrayCollection();
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

    public function getValidityDate(): ?DateTimeImmutable
    {
        return $this->validity_date;
    }

    public function setValidityDate(DateTimeImmutable $validity_date): static
    {
        $this->validity_date = $validity_date;
        return $this;
    }

    /**
     * @return Collection<int, GroundCrewMember>
     */
    public function getGroundCrewMembers(): Collection
    {
        return $this->groundCrewMembers;
    }

    public function addGroundCrewMember(GroundCrewMember $groundCrewMember): static
    {
        if (!$this->groundCrewMembers->contains($groundCrewMember)) {
            $this->groundCrewMembers->add($groundCrewMember);
            $groundCrewMember->addCertification($this);
        }
        return $this;
    }

    public function removeGroundCrewMember(GroundCrewMember $groundCrewMember): static
    {
        $this->groundCrewMembers->removeElement($groundCrewMember);
        return $this;
    }
}
