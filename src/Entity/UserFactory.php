<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * @codeCoverageIgnore
 */
class UserFactory
{
    public function create(): User
    {
        return new User();
    }
}
