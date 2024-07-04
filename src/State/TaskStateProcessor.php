<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroundCrewMember;
use App\Entity\Task;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class TaskStateProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Task
    {
        if (!$data instanceof Task) {
            throw new InvalidArgumentException('Data is not an instance of Task');
        }

        $this->validateTask($data);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    private function validateTask(Task $task): void
    {
        $assignedMember = $task->getAssignedTo();

        if (!$assignedMember) {
            return;
        }

        $this->validateSkills($task, $assignedMember);
        $this->validateCertifications($task, $assignedMember);
    }

    private function validateSkills(Task $task, GroundCrewMember $assignedMember): void
    {
        $requiredSkills = $task->getRequiredSkills();
        $memberSkills = $assignedMember->getSkills();

        foreach ($requiredSkills as $skill) {
            if (!$memberSkills->contains($skill)) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Assigned member does not have required skill: %s',
                    $skill->getName()
                ));
            }
        }
    }

    private function validateCertifications(Task $task, GroundCrewMember $assignedMember): void
    {
        $requiredCertifications = $task->getRequiredCertifications();
        $memberCertifications = $assignedMember->getCertifications();
        $now = new DateTimeImmutable();

        foreach ($requiredCertifications as $cert) {
            $memberCert = $memberCertifications->filter(function($c) use ($cert) {
                return $c->getId() === $cert->getId();
            })->first();

            if (!$memberCert) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Assigned member does not have required certification: %s',
                    $cert->getName()
                ));
            }

            if ($memberCert->getValidityDate() <= $now) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Assigned member\'s certification has expired: %s',
                    $cert->getName()
                ));
            }
        }
    }
}
