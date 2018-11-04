<?php

namespace App\ActionHandler;

class ConfigFactory
{
    /**
     * @param array $data
     * @return Config
     */
    public function create(array $data)
    {
        return new Config($data);
    }
}
