<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CreateUserCommand;
use App\Entity\User;
use App\Entity\UserManagement;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    /**
     * @var UserManagement|MockObject
     */
    private $userManagementMock;

    protected function setUp()
    {
        parent::setUp();
        $this->userManagementMock = $this->createMock(UserManagement::class);
    }

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CreateUserCommand($this->userManagementMock));

        $command = $application->find('user:create');
        $commandTester = new CommandTester($command);

        $email = 'test@example.com';
        $user = new User();
        $user->setEmail($email);
        $this->userManagementMock->method('createUser')->willReturn($user);

        $commandTester->setInputs([$email, '123123q']);

        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains(sprintf('Created user: %s', $email), $output);
    }

    public function testExecuteInvalidInput()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new CreateUserCommand($this->userManagementMock));

        $command = $application->find('user:create');
        $commandTester = new CommandTester($command);

        $email = 'test@example.com';
        $user = new User();
        $user->setEmail($email);
        $this->userManagementMock->method('createUser')->willReturn($user);

        $commandTester->setInputs([$email, '']);

        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('The value cannot be empty', $output);
    }
}
