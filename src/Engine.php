<?php

namespace League\Plates;

use JetBrains\PhpStorm\Pure;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Data;
use League\Plates\Template\Folders;
use League\Plates\Template\Func;
use League\Plates\Template\Functions;
use League\Plates\Template\Name;
use League\Plates\Template\Template;
use LogicException;
use Throwable;

/**
 * Template API and environment settings storage.
 */
class Engine
{
    /**
     * Default template directory.
     */
    protected ?string $directory;

    /**
     * Collection of template folders.
     */
    protected Folders $folders;

    /**
     * Collection of template functions.
     */
    protected Functions $functions;

    /**
     * Collection of preassigned template data.
     */
    protected Data $data;

    /**
     * Create new Engine instance.
     */
    public function __construct(string $directory = null, protected ?string $fileExtension = 'phtml')
    {
        $this->setDirectory($directory);
        $this->folders = new Folders();
        $this->functions = new Functions();
        $this->data = new Data();
    }

    /**
     * Get path to templates directory.
     */
    #[Pure]
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * Set path to templates directory.
     *
     * @param  string|null  $directory Pass null to disable the default directory.
     */
    public function setDirectory(?string $directory): Engine
    {
        if (! is_null($directory) && ! is_dir($directory)) {
            throw new LogicException("The specified path \"{$directory}\" does not exist.");
        }
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get the template file extension.
     */
    #[Pure]
    public function getFileExtension(): ?string
    {
        return $this->fileExtension;
    }

    /**
     * Set the template file extension.
     *
     * @param  string|null  $fileExtension Pass null to manually set it.
     */
    public function setFileExtension(?string $fileExtension): Engine
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     *
     * @param  bool  $fallback
     */
    public function addFolder(string $name, string $directory, $fallback = false): Engine
    {
        $this->folders->add($name, $directory, $fallback);

        return $this;
    }

    /**
     * Remove a template folder.
     */
    public function removeFolder(string $name): Engine
    {
        $this->folders->remove($name);

        return $this;
    }

    /**
     * Get collection of all template folders.
     */
    public function getFolders(): Folders
    {
        return $this->folders;
    }

    /**
     * Add preassigned template data.
     *
     * @param  array  $data ;
     * @param  null|string|array  $templates ;
     */
    public function addData(array $data, $templates = null): Engine
    {
        $this->data->add($data, $templates);

        return $this;
    }

    /**
     * Get all preassigned template data.
     *
     * @param  null|string  $template ;
     */
    #[Pure]
    public function getData($template = null): array
    {
        return $this->data->get($template);
    }

    /**
     * Register a new template function.
     */
    public function registerFunction(string $name, callable $callback): Engine
    {
        $this->functions->add($name, $callback);

        return $this;
    }

    /**
     * Remove a template function.
     */
    public function dropFunction(string $name): Engine
    {
        $this->functions->remove($name);

        return $this;
    }

    /**
     * Get a template function.
     */
    public function getFunction(string $name): Func
    {
        return $this->functions->get($name);
    }

    /**
     * Check if a template function exists.
     */
    public function doesFunctionExist(string $name): bool
    {
        return $this->functions->exists($name);
    }

    /**
     * Load multiple extensions.
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
     * Check if a template exists.
     */
    public function exists(string $name): bool
    {
        return (new Name($this, $name))->doesPathExist();
    }

    /**
     * Create a new template and render it.
     *
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
