<?php

namespace League\Plates\Template;

use LogicException;

/**
 * A template folder.
 */
class Folder
{
    /**
     * The folder name.
     */
    protected string $name;

    /**
     * The folder path.
     */
    protected string $path;

    /**
     * The folder fallback status.
     */
    protected bool $fallback;

    /**
     * Create a new Folder instance.
     *
     * @param  bool  $fallback
     */
    public function __construct(string $name, string $path, $fallback = false)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setFallback($fallback);
    }

    /**
     * Get the folder name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the folder name.
     */
    public function setName(string $name): Folder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the folder path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the folder path.
     */
    public function setPath(string $path): Folder
    {
        if (! is_dir($path)) {
            throw new LogicException("The specified directory path \"{$path}\" does not exist.");
        }

        $this->path = $path;

        return $this;
    }

    /**
     * Get the folder fallback status.
     */
    public function getFallback(): bool
    {
        return $this->fallback;
    }

    /**
     * Set the folder fallback status.
     */
    public function setFallback(bool $fallback): Folder
    {
        $this->fallback = $fallback;

        return $this;
    }
}
