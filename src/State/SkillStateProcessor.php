<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class SkillStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    /**
     * @return Skill|void
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Skill) {
            throw new InvalidArgumentException('Data is not an instance of Skill');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
