<?php
declare(strict_types=1);

namespace App\ActionHandler;

interface TaskInterface
{
    public function execute(array $data): void;
}
