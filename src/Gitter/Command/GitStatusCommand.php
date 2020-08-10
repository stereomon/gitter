<?php

namespace Gitter\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Process\Process;

class GitStatusCommand extends AbstractCommand
{
    public const CONFIG = 'config';
    public const CONFIG_SHORTCUT = 'c';

    public const REPOSITORY = 'repository';
    public const REPOSITORY_SHORTCUT = 'r';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('status')
            ->setDescription('Prints a table with status information about repositories.')
            ->addOption(
                static::CONFIG,
                static::CONFIG_SHORTCUT,
                InputOption::VALUE_REQUIRED,
                'Path to a configuration file (default: .brancho)',
                getcwd() . '/.gitter'
            )
            ->addOption(
                static::REPOSITORY,
                static::REPOSITORY_SHORTCUT,
                InputOption::VALUE_REQUIRED,
                'Use this option to get info for a single repository'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $gitter = $this->getFactory()->createGitter();
        $repositoriesStatus = $gitter->getRepositoriesStatus($input, $output);

        $table = new Table($output);
        $table->setHeaders(['Composer name', 'Current branch name', 'Status']);
        foreach ($repositoriesStatus as $repositoryStatus) {
            $table->addRow([
                $repositoryStatus['composer-name'],
                $repositoryStatus['branch-name'],
                $repositoryStatus['status'],
            ]);
            $table->addRow(new TableSeparator());
        }

        $table->render();

        return static::CODE_SUCCESS;
    }

    /**
     * @return Table
     */
    protected function getTableHelper(): Table
    {
        $tableHelper = $this->getHelper('Table');

        return $tableHelper;
    }
}
