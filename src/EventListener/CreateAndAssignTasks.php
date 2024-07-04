<?php

namespace App\EventListener;

use App\Entity\Flight;
use App\Entity\GroundCrewMember;
use App\Entity\Task;
use App\Repository\GroundCrewMemberRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
class CreateAndAssignTasks
{
    public function __construct(
        private GroundCrewMemberRepository $groundCrewMemberRepository,
    ) {

    }
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Flight) {
            return;
        }

        $entityManager = $args->getObjectManager();

         $this->createAndAssignTasks($entity, $entityManager);
    }

    private function createAndAssignTasks(Flight $flight, $entityManager): void
    {
        $tasks = $this->getTasksForArrival();

        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->setName($taskData['name']);
            $task->setFlight($flight);

            $member = $this->getAvailableMemberWithSkill($taskData['requiredSkillId']);

            dump($member);

            if ($member) {
                $task->setAssignedTo($member);
            }

            $entityManager->persist($task);
        }

        $entityManager->flush();
    }

    private function getTasksForArrival(): array
    {
        return [
            [
                'name' => 'Guide Aircraft to Gate',
                'requiredSkillId' => 15, // Aircraft Marshalling
            ],
            [
                'name' => 'Unload Baggage',
                'requiredSkillId' => 17, // Baggage Handling
            ],
            [
                'name' => 'Assist Passengers',
                'requiredSkillId' => 28, // Passenger Assistance
            ]
        ];
    }

    private function getAvailableMemberWithSkill(string $skillId): ?GroundCrewMember
    {
        return $this->groundCrewMemberRepository->findOneAvailableMemberWithSkill($skillId);
    }
}
