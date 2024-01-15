<?php

namespace Jinya\Plates;

use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Pure;
use Jinya\Plates\Extension\ExtensionInterface;
use Jinya\Plates\Template\Data;
use Jinya\Plates\Template\Folder;
use Jinya\Plates\Template\Func;
use Jinya\Plates\Template\Functions;
use Jinya\Plates\Template\Name;
use Jinya\Plates\Template\Template;
use LogicException;
use Throwable;

/**
 * Template API and environment settings storage.
 */
class Engine
{
    /**
     * Collection of template folders.
     * @var Folder[]
     * @internal
     */
    public array $folders = [];

    /**
     * Collection of template functions.
     */
    public Functions $functions;

    /**
     * Collection of preassigned template data.
     */
    public Data $data;

    /**
     * Create new Engine instance.
     */
    public function __construct(public string|null $directory = null, public string|null $fileExtension = 'phtml')
    {
        $this->functions = new Functions();
        $this->data = new Data();
    }

    /**
     * Get path to templates directory.
     * @deprecated
     */
    #[Pure]
    #[Deprecated]
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * Set path to templates directory.
     * @deprecated
     */
    #[Deprecated]
    public function setDirectory(string|null $directory): Engine
    {
        if ($directory !== null && !is_dir($directory)) {
            throw new LogicException("The specified path \"$directory\" does not exist.");
        }
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get the template file extension.
     * @deprecated
     */
    #[Pure]
    #[Deprecated]
    public function getFileExtension(): string|null
    {
        return $this->fileExtension;
    }

    /**
     * Set the template file extension.
     * @deprecated
     */
    #[Deprecated]
    public function setFileExtension(string|null $fileExtension): Engine
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     */
    public function addFolder(string $name, string $directory, bool $fallback = false): Engine
    {
        $this->folders[$name] = new Folder($directory, $fallback);

        return $this;
    }

    /**
     * Remove a template folder.
     */
    public function removeFolder(string $name): Engine
    {
        unset($this->folders[$name]);

        return $this;
    }

    /**
     * Get collection of all template folders.
     *
     * @return Folder[]
     */
    public function getFolders(): array
    {
        return $this->folders;
    }

    /**
     * Add preassigned template data.
     *
     * @param array<mixed, mixed> $data
     * @param array<string>|string|null $templates
     * @return Engine
     * @deprecated
     */
    #[Deprecated]
    public function addData(array $data, array|string|null $templates = null): Engine
    {
        $this->data->add($data, $templates);

        return $this;
    }

    /**
     * Get all preassigned template data.
     * @return array<mixed, mixed>
     * @deprecated
     */
    #[Pure]
    #[Deprecated]
    public function getData(string|null $template = null): array
    {
        return $this->data->get($template);
    }

    /**
     * Register a new template function.
     * @deprecated
     */
    #[Deprecated]
    public function registerFunction(string $name, callable $callback): Engine
    {
        $this->functions->add($name, $callback);

        return $this;
    }

    /**
     * Remove a template function.
     * @deprecated
     */
    #[Deprecated]
    public function dropFunction(string $name): Engine
    {
        $this->functions->remove($name);

        return $this;
    }

    /**
     * Get a template function.
     * @deprecated
     */
    #[Deprecated]
    public function getFunction(string $name): Func
    {
        return $this->functions->get($name);
    }

    /**
     * Check if a template function exists.
     * @deprecated
     */
    #[Deprecated]
    public function doesFunctionExist(string $name): bool
    {
        return $this->functions->exists($name);
    }

    /**
     * Check if a template exists.
     */
    public function exists(string $name): bool
    {
        return (new Name($this, $name))->doesPathExist();
    }

    /**
     * Load multiple extensions.
     *
     * @param ExtensionInterface[] $extensions
     */
    public function loadExtensions(array $extensions = []): Engine
    {
        foreach ($extensions as $extension) {
            $this->loadExtension($extension);
        }

        return $this;
    }

    /**
     * Load an extension.
     */
    public function loadExtension(ExtensionInterface $extension): Engine
    {
        $extension->register($this);

        return $this;
    }

    /**
     * Get a template path.
     */
    public function path(string $name): string
    {
        return (new Name($this, $name))->getPath();
    }

    /**
     * Create a new template and render it.
     *
     * @param array<mixed> $data
     * @throws Throwable
     */
    public function render(string $name, array $data = []): string
    {
        return $this->make($name)->render($data);
    }

    /**
     * Create a new template.
     */
    public function make(string $name): Template
    {
        return new Template($this, $name);
    }
}
