<?php

namespace Gitter;

use Gitter\Config\Config;
use Gitter\Config\ConfigInterface;
use Gitter\Config\Reader\ConfigReader;
use Gitter\Config\Reader\ConfigReaderInterface;

class GitterFactory
{
    /**
     * @return Gitter
     */
    public function createGitter(): Gitter
    {
        return new Gitter($this->createConfig(), $this);
    }

    /**
     * @return ConfigInterface
     */
    public function createConfig(): ConfigInterface
    {
        return new Config($this->createConfigReader());
    }

    /**
     * @return ConfigReaderInterface
     */
    public function createConfigReader(): ConfigReaderInterface
    {
        return new ConfigReader();
    }
}
