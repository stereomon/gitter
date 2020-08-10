<?php

namespace Gitter;

use Gitter\Command\GitStatusCommand;
use Gitter\Command\InitCommand;
use Symfony\Component\Console\Application;

class GitterBootstrap extends Application
{
    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'Gitter', $version = '1')
    {
        parent::__construct($name, $version);

        $this->setCatchExceptions(false);
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands(): array
    {
        $commands = parent::getDefaultCommands();

        foreach ($this->getCommands() as $command) {
            $commands[$command->getName()] = $command;
        }

        return $commands;
    }

    /**
     * @return \Symfony\Component\Console\Command\Command[]
     */
    private function getCommands(): array
    {
        return [
            new GitStatusCommand(),
            new InitCommand(),
        ];
    }
}
