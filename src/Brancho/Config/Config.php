<?php

namespace Brancho\Config;

use Brancho\Config\Reader\ConfigReaderInterface;
use Brancho\Resolver\DescriptionResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config implements ConfigInterface
{
    public const PATTERN = 'pattern';
    public const RESOLVERS = 'resolvers';
    public const FILTERS = 'filters';

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
            $config = $this->configReader->read($pathToConfig);
            $optionResolver = $this->getOptionResolver();

            $configurationDirectory = dirname($pathToConfig);
            $localConfigurationPath = $configurationDirectory . '/.brancho.local';

            if (file_exists($localConfigurationPath)) {
                $localConfig = $this->configReader->read($localConfigurationPath);
                $config = array_merge($config, $localConfig);
            }

            $this->config = $optionResolver->resolve($config);
        }

        return $this->config;
    }

    /**
     * @return OptionsResolver
     */
    private function getOptionResolver(): OptionsResolver
    {
        $optionResolver = new OptionsResolver();
        $optionResolver->setDefaults([
            static::PATTERN => '{description}',
            static::RESOLVERS => [
                '{description}' => DescriptionResolver::class,
            ],
            static::FILTERS => [],
            'jira' => [
                'host' => '',
                'username' => '',
                'password' => '',
            ],
        ]);

        return $optionResolver;
    }
}
