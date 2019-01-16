<?php
declare(strict_types=1);

namespace App\ActionHandler;

class PermissionValidator
{
    /**
     * @param string $taskClass
     * @param Config $config
     * @return bool
     */
    public function isAllowAction(string $taskClass, Config $config): bool
    {
        $path = array_slice(explode('\\', $taskClass), 3);
        $path = array_map('lcfirst', $path);

        return $config->hasValue(implode('/', $path));
    }
}
