<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Certification;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class CertificateStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Certification
    {
        if (!$data instanceof Certification) {
            throw new InvalidArgumentException('Data is not an instance of Certification');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
