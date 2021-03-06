<?php

namespace League\Plates\Template;

use LogicException;

/**
 * A collection of template folders.
 */
class Folders
{
    /**
     * Array of template folders.
     * @var array
     */
    protected array $folders = [];

    /**
     * Add a template folder.
     * @param string $name
     * @param string $path
     * @param bool $fallback
     * @return Folders
     */
    public function add(string $name, string $path, $fallback = false): Folders
    {
        if ($this->exists($name)) {
            throw new LogicException("The template folder \"{$name}\" is already being used.");
        }

        $this->folders[$name] = new Folder($name, $path, $fallback);

        return $this;
    }

    /**
     * Check if a template folder exists.
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return isset($this->folders[$name]);
    }

    /**
     * Remove a template folder.
     * @param string $name
     * @return Folders
     */
    public function remove(string $name): Folders
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template folder \"{$name}\" was not found.");
        }

        unset($this->folders[$name]);

        return $this;
    }

    /**
     * Get a template folder.
     * @param string $name
     * @return Folder
     */
    public function get(string $name): Folder
    {
        if (!$this->exists($name)) {
            throw new LogicException("The template folder \"{$name}\" was not found.");
        }

        return $this->folders[$name];
    }
}
