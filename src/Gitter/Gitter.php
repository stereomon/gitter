<?php

namespace Gitter;

use Gitter\Command\GitStatusCommand;
use Gitter\Config\Config;
use Gitter\Config\ConfigInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Gitter
{
    /**
     * @var string[]
     */
    protected $resolvedElements = [];

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var GitterFactory
     */
    protected $factory;

    /**
     * @param ConfigInterface $config
     * @param GitterFactory $factory
     */
    public function __construct(ConfigInterface $config, GitterFactory $factory)
    {
        $this->config = $config;
        $this->factory = $factory;
    }

    /**
     * @return array
     */
    public function getRepositoriesStatus(InputInterface $input, OutputInterface $output): array
    {
        $config = $this->loadConfig($this->getConfigPath($input));

        $repositories = $config[Config::REPOSITORIES];

        $repositoriesStatus = [];

        foreach ($repositories as $repositoryPath) {
            $repositoriesStatus[] = $this->getStatusForRepository($repositoryPath);
        }

        return $repositoriesStatus;
    }

    /**
     * @param string $repositoryPath
     *
     * @return array
     */
    protected function getStatusForRepository(string $repositoryPath): array
    {
        $branchName = $this->getBranchName($repositoryPath);

        return [
            'composer-name' => $this->getComposerName($repositoryPath),
            'branch-name' => $branchName,
            'status' => $this->getStatus($repositoryPath, $branchName),
        ];
    }

    /**
     * @param string $repositoryPath
     *
     * @return string
     */
    protected function getComposerName(string $repositoryPath): string
    {
        $pathToComposerJson = sprintf('%s/composer.json');
        $composerJsonAsArray = json_decode(file_get_contents($pathToComposerJson), true);

        return $composerJsonAsArray['name'];
    }

    /**
     * @param string $repositoryPath
     *
     * @return string
     */
    protected function getBranchName(string $repositoryPath): string
    {
        $process = new Process(['git', 'rev-parse', 'abbrev-ref', 'HEAD'], $repositoryPath);
        $branchName = trim($process->run());

        return $branchName;
    }

    /**
     * @param string $repositoryPath
     * @param string $branchName
     *
     * @return string
     */
    protected function getStatus(string $repositoryPath, string $branchName): string
    {
        return 'Not implemented yet';

        $process = new Process(['git', 'rev-parse', 'abbrev-ref', 'HEAD'], $repositoryPath);
        $branchName = trim($process->run());

        return $branchName;
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getConfigPath(InputInterface $input): string
    {
        /** @var string $configPath */
        $configPath = $input->getOption(GitStatusCommand::CONFIG);

        return $configPath;
    }

    /**
     * @param string $pathToConfig
     *
     * @return array
     */
    protected function loadConfig(string $pathToConfig): array
    {
        return $this->config->load($pathToConfig);
    }
}
