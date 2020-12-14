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
     * @var Engine
     */
    protected Engine $engine;

    /**
     * The original name.
     * @var string
     */
    protected string $name;

    /**
     * The parsed template folder.
     * @var Folder
     */
    protected Folder $folder;

    /**
     * The parsed template filename.
     * @var string
     */
    protected string $file;

    /**
     * Create a new Name instance.
     * @param Engine $engine
     * @param string $name
     */
    public function __construct(Engine $engine, string $name)
    {
        $this->setEngine($engine);
        $this->setName($name);
    }

    /**
     * Get the engine.
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }

    /**
     * Set the engine.
     * @param Engine $engine
     * @return Name
     */
    public function setEngine(Engine $engine): Name
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Get the original name.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the original name and parse it.
     * @param string $name
     * @return Name
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
                'The template name "' . $this->name . '" is not valid. ' .
                'Do not use the folder namespace separator "::" more than once.'
            );
        }

        return $this;
    }

    /**
     * Get the parsed template folder.
     * @return Folder|string
     */
    public function getFolder(): Folder|string
    {
        return $this->folder ?? '';
    }

    /**
     * Set the parsed template folder.
     * @param string $folder
     * @return Name
     */
    public function setFolder(string $folder): Name
    {
        $this->folder = $this->engine->getFolders()->get($folder);

        return $this;
    }

    /**
     * Get the parsed template file.
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Set the parsed template file.
     * @param string $file
     * @return Name
     */
    public function setFile(string $file): Name
    {
        if ($file === '') {
            throw new LogicException(
                'The template name "' . $this->name . '" is not valid. ' .
                'The template name cannot be empty.'
            );
        }

        $this->file = $file;

        if (!is_null($this->engine->getFileExtension())) {
            $this->file .= '.' . $this->engine->getFileExtension();
        }

        return $this;
    }

    /**
     * Check if template path exists.
     * @return boolean
     */
    public function doesPathExist(): bool
    {
        return is_file($this->getPath());
    }

    /**
     * Resolve template path.
     * @return string
     */
    public function getPath(): string
    {
        if (!isset($this->folder) || is_null($this->folder)) {
            return $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file;
        }

        $path = $this->folder->getPath() . DIRECTORY_SEPARATOR . $this->file;

        if (!is_file($path) && $this->folder->getFallback() && is_file(
                $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file
            )) {
            $path = $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file;
        }

        return $path;
    }

    /**
     * Get the default templates directory.
     * @return string
     */
    protected function getDefaultDirectory(): string
    {
        $directory = $this->engine->getDirectory();

        if (is_null($directory)) {
            throw new LogicException(
                'The template name "' . $this->name . '" is not valid. ' .
                'The default directory has not been defined.'
            );
        }

        return $directory;
    }
}
