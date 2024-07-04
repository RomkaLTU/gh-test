<?php

namespace App\Command;

use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed-skills',
    description: 'Add a short description for your command',
)]
class SeedSkillsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $skills = [
            'Aircraft Marshalling',
            'Refueling',
            'Baggage Handling',
            'De-icing',
            'Towing',
            'Cabin Cleaning',
            'Catering Logistics',
            'Ground Power Operation',
            'Lavatory Service',
            'Potable Water Service',
            'Tire Pressure Check',
            'FOD Detection',
            'GPU Operation',
            'Passenger Assistance',
            'Weight and Balance Calculation',
            'Cargo Loading',
            'Aircraft External Inspection',
            'Fuel Quality Testing',
            'Emergency Response',
            'Radio Communication',
        ];

        foreach ($skills as $skillName) {
            $skill = new Skill();
            $skill->setName($skillName);
            $this->entityManager->persist($skill);
        }

        $this->entityManager->flush();

        $output->writeln('Skills have been seeded successfully.');

        return Command::SUCCESS;
    }
}
