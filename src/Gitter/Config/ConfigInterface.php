<?php

namespace Gitter\Config;

interface ConfigInterface
{
    /**
     * @param string $pathToConfig
     *
     * @return array
     */
    public function load(string $pathToConfig): array;
}
