<?php

namespace GitterTest\Helper;

use Gitter\GitterBootstrap;
use Codeception\Module;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CommandHelper extends Module
{

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    public function getConsoleTester(Command $command): CommandTester
    {
        $application = new GitterBootstrap();
        $application->add($command);

        $command = $application->find($command->getName());

        return new CommandTester($command);
    }
}
