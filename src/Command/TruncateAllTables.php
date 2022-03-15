<?php

namespace App\Command;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\FilesLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TruncateAllTables extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
        parent::__construct('app:database:truncate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Truncating all tables');

        $connection = $this->entityManager->getConnection();
        $tables = $connection->createSchemaManager()->listTables();
        $databasePlatform = $connection->getDatabasePlatform();

        foreach($tables as $table) {
            $truncateQuery = $databasePlatform->getTruncateTableSQL($table->getName(), true);
            $connection->executeStatement($truncateQuery);
        }

        $io->success('Successfully truncated all tables.');

        return Command::SUCCESS;
    }
}