<?php

namespace GitterTest\GitterTest\Command;

use Gitter\GitterFactory;
use Gitter\Command\GitStatusCommand;
use Gitter\Jira\Jira;
use Codeception\Stub;
use Codeception\Test\Unit;

class GitStatusCommandTest extends Unit
{
    /**
     * @var CommandTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testSimplePattern(): void
    {
        $gitStatusCommand = new GitStatusCommand();

        $commandTester = $this->tester->getConsoleTester($gitStatusCommand);
        $commandTester->execute(['--config' => codecept_data_dir('repository.yml')]);

        $this->assertStringContainsString('branch-description', $commandTester->getDisplay());
        $this->assertStringContainsString('"branch-description" created.', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testCommandTypePattern(): void
    {
        $branchBuilderCommand = new GitStatusCommand();

        $commandTester = $this->tester->getConsoleTester($branchBuilderCommand);
        $commandTester->setInputs(['feature', 'Branch description']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-type-and-description.yml')]);

        $this->assertStringContainsString('feature/branch-description', $commandTester->getDisplay());
        $this->assertStringContainsString('"feature/branch-description" created.', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testCommandJiraPattern(): void
    {
        $jiraMock = Stub::make(Jira::class, [
            'getJiraIssue' => function () {
                return include codecept_data_dir('jira-bug-response.php');
            },
        ]);

        /** @var $factoryMock GitterFactory */
        $factoryMock = Stub::make(GitterFactory::class, [
            'createJira' => function () use ($jiraMock) {
                return $jiraMock;
            },
        ]);

        $branchBuilderCommand = new GitStatusCommand();
        $branchBuilderCommand->setFactory($factoryMock);

        $commandTester = $this->tester->getConsoleTester($branchBuilderCommand);
        $commandTester->setInputs(['rk-123']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-jira.yml')]);

        $this->assertStringContainsString('bugfix/rk-123/ticket-summary', $commandTester->getDisplay());
        $this->assertStringContainsString('"bugfix/rk-123/ticket-summary" created.', $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testCommandJiraPatternWithErrorResponse(): void
    {
        $jiraMock = Stub::make(Jira::class, [
            'getJiraIssue' => function () {
                return include codecept_data_dir('jira-error-response.php');
            },
        ]);

        /** @var $factoryMock GitterFactory */
        $factoryMock = Stub::make(GitterFactory::class, [
            'createJira' => function () use ($jiraMock) {
                return $jiraMock;
            },
        ]);

        $branchBuilderCommand = new GitStatusCommand();
        $branchBuilderCommand->setFactory($factoryMock);

        $commandTester = $this->tester->getConsoleTester($branchBuilderCommand);
        $commandTester->setInputs(['rk-123']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-jira.yml')]);

        $this->assertStringContainsString(
            'Issue does not exist or you do not have permission to see it.',
            $commandTester->getDisplay()
        );
    }

    /**
     * @return void
     */
    public function testExecuteOnlyShowsBranchName(): void
    {
        $branchBuilderCommand = new GitStatusCommand();

        $commandTester = $this->tester->getConsoleTester($branchBuilderCommand);
        $commandTester->setInputs(['feature', 'Branch description', 'n']);
        $commandTester->execute(['--config' => codecept_data_dir('pattern-type-and-description.yml')]);

        $this->assertStringContainsString('feature/branch-description', $commandTester->getDisplay());
        $this->assertStringContainsString('"feature/branch-description" NOT created.', $commandTester->getDisplay());
    }
}
