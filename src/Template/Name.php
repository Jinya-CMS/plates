<?php

namespace League\Plates\Template;

use League\Plates\Engine;
use LogicException;

/**
 * A template name.
 */
class Name
{
    /**
     * Instance of the template engine.
     */
    protected Engine $engine;

    /**
     * The original name.
     */
    protected string $name;

    /**
     * The parsed template folder.
     */
    protected Folder|string $folder;

    /**
     * The parsed template filename.
     */
    protected string $file;

    /**
     * Create a new Name instance.
     */
    public function __construct(Engine $engine, string $name)
    {
        $this->setEngine($engine);
        $this->setName($name);
    }

    /**
     * Get the engine.
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }

    /**
     * Set the engine.
     */
    public function setEngine(Engine $engine): Name
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Get the original name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the original name and parse it.
     */
    public function setName(string $name): Name
    {
        $this->name = $name;

        $parts = explode('::', $this->name);

        if (count($parts) === 1) {
            $this->setFile($parts[0]);
        } elseif (count($parts) === 2) {
            $this->setFolder($parts[0]);
            $this->setFile($parts[1]);
        } else {
            throw new LogicException(
                "The template name \"{$this->name}\" is not valid. Do not use the folder namespace separator \"::\" more than once."
            );
        }

        return $this;
    }

    /**
     * Get the parsed template folder.
     */
    public function getFolder(): Folder|string
    {
        return $this->folder ?? '';
    }

    /**
     * Set the parsed template folder.
     */
    public function setFolder(string $folder): Name
    {
        $this->folder = $this->engine->getFolders()->get($folder);

        return $this;
    }

    /**
     * Get the parsed template file.
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Set the parsed template file.
     */
    public function setFile(string $file): Name
    {
        if ($file === '') {
            throw new LogicException(
                "The template name \"{$this->name}\" is not valid. The template name cannot be empty."
            );
        }

        $this->file = $file;

        if (! is_null($this->engine->getFileExtension())) {
            $this->file .= '.'.$this->engine->getFileExtension();
        }

        return $this;
    }

    /**
     * Check if template path exists.
     */
    public function doesPathExist(): bool
    {
        return is_file($this->getPath());
    }

    /**
     * Resolve template path.
     */
    public function getPath(): string
    {
        if (! isset($this->folder) || is_null($this->folder)) {
            return $this->getDefaultDirectory().DIRECTORY_SEPARATOR.$this->file;
        }

        $path = $this->folder->getPath().DIRECTORY_SEPARATOR.$this->file;

        if (! is_file($path) && $this->folder->getFallback() && is_file(
            $this->getDefaultDirectory().DIRECTORY_SEPARATOR.$this->file
        )) {
            $path = $this->getDefaultDirectory().DIRECTORY_SEPARATOR.$this->file;
        }

        return $path;
    }

    /**
     * Get the default templates directory.
     */
    protected function getDefaultDirectory(): string
    {
        $directory = $this->engine->getDirectory();

        if (is_null($directory)) {
            throw new LogicException(
                "The template name \"{$this->name}\" is not valid. The default directory has not been defined."
            );
        }

        return $directory;
    }
}
