<?php
declare(strict_types=1);

namespace App\ActionHandler;

class ConfigFactory
{
    /**
     * @param array $data
     * @return Config
     */
    public function create(array $data): Config
    {
        return new Config($data);
    }
}
