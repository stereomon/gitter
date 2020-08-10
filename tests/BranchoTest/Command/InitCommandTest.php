<?php

namespace GitterTest\GitterTest\Command;

use Gitter\Command\InitCommand;
use Codeception\Stub;
use Codeception\Test\Unit;

class InitCommandTest extends Unit
{
    /**
     * @var CommandTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $branchoLocalPath = $this->getGitterLocalPath();
        $branchoLocalDirectory = dirname($branchoLocalPath);

        if (file_exists($branchoLocalPath)) {
            unlink($branchoLocalPath);
        }

        if (is_dir($branchoLocalDirectory)) {
            rmdir($branchoLocalDirectory);
        }
    }

    /**
     * @return string
     */
    protected function getGitterLocalPath(): string
    {
        return codecept_data_dir('home/brancho/.brancho.local');
    }

    /**
     * @return string
     */
    protected function getHomeDirectory(): string
    {
        return codecept_data_dir('home');
    }

    /**
     * @return void
     */
    public function testInitWillNotWriteLocalConfigurationWhenNoResolverUsedWhichNeedsConfiguration(): void
    {
        /** @var InitCommand $initCommand */
        $initCommand = Stub::construct(InitCommand::class, [], [
            'getHomeDirectory' => codecept_data_dir('home'),
        ]);

        $commandTester = $this->tester->getConsoleTester($initCommand);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-description.yml')]);

        $this->assertStringContainsString('No resolver which needs configuration found, configuration not changed.', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testInitWillWriteLocalConfigurationWhenResolverUsedWhichNeedsConfiguration(): void
    {
        /** @var InitCommand $initCommand */
        $initCommand = Stub::construct(InitCommand::class, [], [
            'getHomeDirectory' => codecept_data_dir('home'),
        ]);

        $commandTester = $this->tester->getConsoleTester($initCommand);
        $commandTester->setInputs(['jira-host.com', 'jira-username', 'jira-password']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-jira.yml')]);

        $this->assertStringContainsString('Added configuration to', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testInitWillAskToOverwriteWhenGitterLocalExistsAndAbortIfUserStops(): void
    {
        /** @var InitCommand $initCommand */
        $initCommand = Stub::construct(InitCommand::class, [], [
            'getHomeDirectory' => codecept_data_dir('home'),
        ]);

        $this->createGitterLocalFile();

        $commandTester = $this->tester->getConsoleTester($initCommand);
        $commandTester->setInputs(['n']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-jira.yml')]);

        $this->assertStringContainsString('Aborted creation of brancho local configuration.', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testInitWillAskToOverwriteWhenGitterLocalExists(): void
    {
        /** @var InitCommand $initCommand */
        $initCommand = Stub::construct(InitCommand::class, [], [
            'getHomeDirectory' => codecept_data_dir('home'),
        ]);

        $this->createGitterLocalFile();

        $commandTester = $this->tester->getConsoleTester($initCommand);
        $commandTester->setInputs(['y', 'jira-host.com', 'jira-username', 'jira-password']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-jira.yml')]);

        $this->assertStringContainsString('when you continue, this will be re-written, should I continue?', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    protected function createGitterLocalFile(): void
    {
        if (!is_dir(dirname($this->getGitterLocalPath()))) {
            mkdir(dirname($this->getGitterLocalPath()), 0777, true);
        }

        file_put_contents($this->getGitterLocalPath(), '');
    }
}
