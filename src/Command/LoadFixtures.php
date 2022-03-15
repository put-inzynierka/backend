<?php

namespace App\Command;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\FilesLoaderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadFixtures extends Command
{
    protected Finder $finder;
    protected FilesLoaderInterface $fixturesLoader;
    protected EntityManagerInterface $entityManager;
    protected string $fixturesDirectory;

    public function __construct(EntityManagerInterface $entityManager, string $fixturesDirectory)
    {
        parent::__construct('app:fixtures:load');

        $this->finder = new Finder();
        $this->fixturesLoader = new NativeLoader();
        $this->entityManager = $entityManager;
        $this->fixturesDirectory = $fixturesDirectory;
    }

    protected function configure()
    {
        $this
            ->setAliases(['hautelook:fixtures:load'])
            ->addOption('append', 'a')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Loading data fixtures');

        if (!$input->getOption('append')) {
            $io->writeln('<info>Truncating all tables</info>');
            $this->truncateAllTables();
        }

        $io->writeln('<info>Loading fixtures</info>');
        $filesToLoad = $this->findFixtureFiles();
        $objects = $this->fixturesLoader->loadFiles($filesToLoad)->getObjects();
        $this->persistObjects($objects, $io);

        $io->success('Successfully loaded the fixtures.');

        return Command::SUCCESS;
    }

    private function truncateAllTables(): int
    {
        $input = new ArrayInput([]);
        $output = new NullOutput();

        return $this->getApplication()->find('app:database:truncate')->run($input, $output);
    }

    private function findFixtureFiles(): array
    {
        $finderResults = $this->finder->name(['*.yml', '*.yaml'])->in($this->fixturesDirectory);

        return iterator_to_array($finderResults);
    }

    private function persistObjects(array $objects, SymfonyStyle $io): void
    {
        foreach ($objects as $object) {
            try {
                $this->entityManager->persist($object);
            } catch (Exception $exception) {
                $io->writeln('<fg=black;bg=yellow>' . $exception->getMessage() . '</>');
            }
        }
        $this->entityManager->flush();
    }
}