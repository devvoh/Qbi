<?php

/**
 * Borrowed from Parable - https://github.com/devvoh/parable
 */

namespace Qbi;

class DI
{
    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @var array
     */
    protected static $relations = [];

    public static function get($className, $parentClassName = '')
    {
        // We store the relationship between class & parent to prevent cyclical references
        if ($parentClassName) {
            self::$relations[$className][$parentClassName] = true;
        }

        // And we check for cyclical references to prevent infinite loops
        if ($parentClassName
            && isset(self::$relations[$parentClassName])
            && isset(self::$relations[$parentClassName][$className])
        ) {
            $message  = 'Cyclical dependency found: ' . $className . ' depends on ' . $parentClassName;
            $message .= ' but is itself a dependency of ' . $parentClassName . '.';
            throw new \Exception($message);
        }

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = self::create($className, $parentClassName);
        }

        return self::$instances[$className];
    }

    public static function create(string $className, string $parentClassName = '')
    {
        if (!class_exists($className)) {
            $message = 'Could not create instance of "' . $className . '"';
            if ($parentClassName) {
                $message .= ', required by "' . $parentClassName . '"';
            }
            throw new \Exception($message);
        }
        $reflection = new \ReflectionClass($className);
        /** @var \ReflectionMethod $construct */
        $construct = $reflection->getConstructor();

        if (!$construct) {
            return new $className();
        }

        /** @var \ReflectionParameter[] $parameters */
        $parameters = $construct->getParameters();

        $dependencies = [];
        foreach ($parameters as $parameter) {
            $subClassName = $parameter->name;
            if ($parameter->getClass()) {
                $subClassName = $parameter->getClass()->name;
            }
            $dependencies[] = self::get($subClassName, $className);
        }
        return (new \ReflectionClass($className))->newInstanceArgs($dependencies);
    }

    /**
     * Store an instance under either the provided $name or its class name.
     *
     * @param object      $instance
     * @param string|null $name
     */
    public static function store($instance, string $name = '')
    {
        if ($name === '') {
            $name = get_class($instance);
        }
        self::$instances[$name] = $instance;
    }
}
