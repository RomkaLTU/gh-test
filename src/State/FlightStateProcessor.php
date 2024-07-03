<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Flight;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

readonly class FlightStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Flight
    {
        if (!$data instanceof Flight) {
            throw new InvalidArgumentException('Data is not an instance of Flight');
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
