<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\UserFactory;
use App\Entity\UserManagement;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagementTest extends TestCase
{
    public function testCreateUser()
    {
        $password = 'wowSuchHash';
        $userFactory = new UserFactory();
        $encoderMock = $this->createMock(UserPasswordEncoderInterface::class);
        $encoderMock->method('encodePassword')->willReturn($password);
        $userManagement = new UserManagement(
            $this->createMock(EntityManagerInterface::class),
            $encoderMock,
            $userFactory
        );

        $expected = $userFactory->create();
        $expected->setEmail('test@example.com')
            ->setPassword($password)
            ->setRoles(['ROLE_ADMIN']);

        $result = $userManagement->createUser($expected->getEmail(), $expected->getPassword());

        $this->assertEquals($expected, $result);
    }
}
