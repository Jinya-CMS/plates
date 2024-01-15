<?php

namespace Jinya\Plates\Template;

use LogicException;

/**
 * A collection of template functions.
 * @internal
 */
class Functions
{
    /**
     * Array of template functions.
     * @var Func[]
     */
    private array $functions = [];

    /**
     * Add a new template function.
     */
    public function add(string $name, callable $callback): void
    {
        if ($this->exists($name)) {
            throw new LogicException("The template function name \"$name\" is already registered.");
        }

        $this->functions[$name] = new Func($name, $callback);
    }

    /**
     * Check if a template function exists.
     */
    public function exists(string $name): bool
    {
        return isset($this->functions[$name]);
    }

    /**
     * Remove a template function.
     *
     * @param string $name ;
     */
    public function remove(string $name): void
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template function \"$name\" was not found.");
        }

        unset($this->functions[$name]);
    }

    /**
     * Get a template function.
     */
    public function get(string $name): Func
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template function \"$name\" was not found.");
        }

        return $this->functions[$name];
    }
}
