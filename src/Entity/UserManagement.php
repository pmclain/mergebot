<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagement
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserFactory
     */
    private $userFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserFactory $userFactory
    ) {
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(string $email, string $password): User
    {
        $user = $this->userFactory->create();
        $user->setEmail($email);

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        //TODO: implement
    }
}
