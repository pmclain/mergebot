<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TokenGenerator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function generate(User $user): User
    {
        $user->setApiToken(sha1(random_bytes(32)));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
