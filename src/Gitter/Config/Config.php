<?php

namespace Gitter\Config;

use Gitter\Config\Reader\ConfigReaderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config implements ConfigInterface
{
    public const REPOSITORIES = 'repositories';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ConfigReaderInterface
     */
    protected $configReader;

    /**
     * @param ConfigReaderInterface $configReader
     */
    public function __construct(ConfigReaderInterface $configReader)
    {
        $this->configReader = $configReader;
    }

    /**
     * @param string $pathToConfig
     *
     * @return array
     */
    public function load(string $pathToConfig): array
    {
        if ($this->config === null) {
            $this->config = $this->getRootConfiguration($pathToConfig);
        }

        return $this->config;
    }

    /**
     * @param string $pathToConfig
     *
     * @return array
     */
    protected function getRootConfiguration(string $pathToConfig): array
    {
        $config = [];

        if (file_exists($pathToConfig)) {
            $config = $this->configReader->read($pathToConfig);
        }

        return $config;
    }

    /**
     * @return string
     */
    protected function getHomeDirectory(): string
    {
        return (string)getenv('HOME');
    }
}
