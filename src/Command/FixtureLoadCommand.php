<?php

namespace App\Command;

use App\Fixture\FixtureLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'fixture:load',
    description: 'Load fixtures',
)]
class FixtureLoadCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FixtureLoader $fixtureLoader
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'The relative path under fixtures folder',
                'dev'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->truncateTables();


        /**
         * @var string $path
         */
        $path = $input->getOption('path');

        $fixtures = $this->fixtureLoader->locateFiles($path);
        $objects = $this->fixtureLoader->loadFiles($fixtures);

        foreach ($objects as $object) {
            $this->entityManager->persist($object);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }

    private function truncateTables(): void
    {
        $connection = $this->entityManager->getConnection();

        $tableNames = array_filter(
            $connection->createSchemaManager()->listTableNames(),
            fn (string $table) => $table !== 'doctrine_migration_versions'
        );

        $sql = $connection->getDatabasePlatform()->getTruncateTableSql(implode(',', $tableNames));

        $connection->executeStatement($sql);
    }
}
