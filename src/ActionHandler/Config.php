<?php
declare(strict_types=1);

namespace App\ActionHandler;

use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Config
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getValue(string $path)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($this->data, $this->pathToArrayAccessorPath($path));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function hasValue(string $path): bool
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        try {
            $propertyAccessor->getValue($this->data, $this->pathToArrayAccessorPath($path));
            return true;
        } catch (NoSuchIndexException $e) {
            return false;
        }
    }

    private function pathToArrayAccessorPath(string $path): string
    {
        return '[' . str_replace('/', '][', $path) . ']';
    }
}
