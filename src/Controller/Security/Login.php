<?php
declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Login extends AbstractController
{
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        TokenGenerator $tokenGenerator
    ) {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function execute(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->tokenGenerator->generate($user);

        return new JsonResponse([
            'token' => $user->getApiToken(),
        ]);
    }
}
