<?php
declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Login extends AbstractController
{
    public function execute(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
        ]);
    }
}
