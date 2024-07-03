<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroundCrewMember;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class GroundCrewMemberStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroundCrewMember
    {
        if (!$data instanceof GroundCrewMember) {
            throw new InvalidArgumentException('Data is not an instance of GroundCrewMember');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
