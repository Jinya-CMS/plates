<?php

namespace Jinya\Plates\Template;

use Jinya\Plates\Engine;
use LogicException;

/**
 * A template name.
 * @internal
 */
class Name
{
    /**
     * The original name.
     */
    public string $name;

    /**
     * The parsed template folder.
     */
    private Folder|null $folder = null;

    /**
     * The parsed template filename.
     */
    private string $file;

    /**
     * Create a new Name instance.
     * @param Engine $engine Instance of the template engine.
     */
    public function __construct(public Engine $engine, string $name)
    {
        $this->setName($name);
    }

    /**
     * Set the original name and parse it.
     */
    private function setName(string $name): void
    {
        $this->name = $name;

        $parts = explode('::', $this->name);

        if (count($parts) === 1) {
            $this->setFile($parts[0]);
        } elseif (count($parts) === 2) {
            $this->folder = $this->engine->folders[$parts[0]];
            $this->setFile($parts[1]);
        } else {
            throw new LogicException(
                "The template name \"$this->name\" is not valid. Do not use the folder namespace separator \"::\" more than once."
            );
        }
    }

    /**
     * Set the parsed template file.
     */
    private function setFile(string $file): void
    {
        if ($file === '') {
            throw new LogicException(
                "The template name \"$this->name\" is not valid. The template name cannot be empty."
            );
        }

        $this->file = $file;

        if ($this->engine->fileExtension !== null) {
            $this->file .= '.' . $this->engine->fileExtension;
        }
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
        if ($this->folder === null) {
            return $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file;
        }

        $path = $this->folder->path . DIRECTORY_SEPARATOR . $this->file;

        if ($this->folder->fallback && !is_file($path) && is_file(
            $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file
        )) {
            $path = $this->getDefaultDirectory() . DIRECTORY_SEPARATOR . $this->file;
        }

        return $path;
    }

    /**
     * Get the default templates directory.
     */
    private function getDefaultDirectory(): string
    {
        $directory = $this->engine->directory;

        return $directory ?? throw new LogicException(
            "The template name \"$this->name\" is not valid. The default directory has not been defined."
        );
    }
}
