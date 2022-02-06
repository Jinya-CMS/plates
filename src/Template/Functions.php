<?php

namespace League\Plates\Template;

use LogicException;

/**
 * A collection of template functions.
 */
class Functions
{
    /**
     * Array of template functions.
     * @var array
     */
    protected array $functions = [];

    /**
     * Add a new template function.
     * @param string $name ;
     * @param callback $callback ;
     * @return Functions
     */
    public function add(string $name, callable $callback): Functions
    {
        if ($this->exists($name)) {
            throw new LogicException("The template function name \"{$name}\" is already registered.");
        }

        $this->functions[$name] = new Func($name, $callback);

        return $this;
    }

    /**
     * Check if a template function exists.
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return isset($this->functions[$name]);
    }

    /**
     * Remove a template function.
     * @param string $name ;
     * @return Functions
     */
    public function remove(string $name): Functions
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template function \"{$name}\" was not found.");
        }

        unset($this->functions[$name]);

        return $this;
    }

    /**
     * Get a template function.
     * @param string $name
     * @return Func
     */
    public function get(string $name): Func
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template function \"{$name}\" was not found.");
        }

        return $this->functions[$name];
    }
}
