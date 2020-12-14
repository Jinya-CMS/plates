<?php

namespace League\Plates;

use JetBrains\PhpStorm\Pure;
use League\Plates\Extension\ExtensionInterface;
use League\Plates\Template\Data;
use League\Plates\Template\Directory;
use League\Plates\Template\FileExtension;
use League\Plates\Template\Folders;
use League\Plates\Template\Func;
use League\Plates\Template\Functions;
use League\Plates\Template\Name;
use League\Plates\Template\Template;
use Throwable;

/**
 * Template API and environment settings storage.
 */
class Engine
{
    /**
     * TODO: Replace with string field
     * Default template directory.
     * @var Directory
     */
    protected Directory $directory;

    /**
     * TODO: Replace with string field
     * Template file extension.
     * @var FileExtension
     */
    protected FileExtension $fileExtension;

    /**
     * Collection of template folders.
     * @var Folders
     */
    protected Folders $folders;

    /**
     * Collection of template functions.
     * @var Functions
     */
    protected Functions $functions;

    /**
     * Collection of preassigned template data.
     * @var Data
     */
    protected Data $data;

    /**
     * Create new Engine instance.
     * @param string $directory
     * @param string $fileExtension
     */
    public function __construct($directory = null, $fileExtension = 'php')
    {
        $this->directory = new Directory($directory);
        $this->fileExtension = new FileExtension($fileExtension);
        $this->folders = new Folders();
        $this->functions = new Functions();
        $this->data = new Data();
    }

    /**
     * Get path to templates directory.
     * @return string|null
     */
    #[Pure] public function getDirectory(): ?string
    {
        return $this->directory->get();
    }

    /**
     * Set path to templates directory.
     * @param string|null $directory Pass null to disable the default directory.
     * @return Engine
     */
    public function setDirectory(?string $directory): Engine
    {
        $this->directory->set($directory);

        return $this;
    }

    /**
     * Get the template file extension.
     * @return string|null
     */
    #[Pure] public function getFileExtension(): ?string
    {
        return $this->fileExtension->get();
    }

    /**
     * Set the template file extension.
     * @param string|null $fileExtension Pass null to manually set it.
     * @return Engine
     */
    public function setFileExtension(?string $fileExtension): Engine
    {
        $this->fileExtension->set($fileExtension);

        return $this;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     * @param string $name
     * @param string $directory
     * @param bool $fallback
     * @return Engine
     */
    public function addFolder(string $name, string $directory, $fallback = false): Engine
    {
        $this->folders->add($name, $directory, $fallback);

        return $this;
    }

    /**
     * Remove a template folder.
     * @param string $name
     * @return Engine
     */
    public function removeFolder(string $name): Engine
    {
        $this->folders->remove($name);

        return $this;
    }

    /**
     * Get collection of all template folders.
     * @return Folders
     */
    public function getFolders(): Folders
    {
        return $this->folders;
    }

    /**
     * Add preassigned template data.
     * @param array $data ;
     * @param null|string|array $templates ;
     * @return Engine
     */
    public function addData(array $data, $templates = null): Engine
    {
        $this->data->add($data, $templates);

        return $this;
    }

    /**
     * Get all preassigned template data.
     * @param null|string $template ;
     * @return array
     */
    #[Pure] public function getData($template = null): array
    {
        return $this->data->get($template);
    }

    /**
     * Register a new template function.
     * @param string $name
     * @param callback $callback
     * @return Engine
     */
    public function registerFunction(string $name, callable $callback): Engine
    {
        $this->functions->add($name, $callback);

        return $this;
    }

    /**
     * Remove a template function.
     * @param string $name
     * @return Engine
     */
    public function dropFunction(string $name): Engine
    {
        $this->functions->remove($name);

        return $this;
    }

    /**
     * Get a template function.
     * @param string $name
     * @return Func
     */
    public function getFunction(string $name): Func
    {
        return $this->functions->get($name);
    }

    /**
     * Check if a template function exists.
     * @param string $name
     * @return bool
     */
    public function doesFunctionExist(string $name): bool
    {
        return $this->functions->exists($name);
    }

    /**
     * Load multiple extensions.
     * @param array $extensions
     * @return Engine
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
     * @param ExtensionInterface $extension
     * @return Engine
     */
    public function loadExtension(ExtensionInterface $extension): Engine
    {
        $extension->register($this);

        return $this;
    }

    /**
     * Get a template path.
     * @param string $name
     * @return string
     */
    public function path(string $name): string
    {
        return (new Name($this, $name))->getPath();
    }

    /**
     * Check if a template exists.
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return (new Name($this, $name))->doesPathExist();
    }

    /**
     * Create a new template and render it.
     * @param string $name
     * @param array $data
     * @return string
     * @throws Throwable
     */
    public function render(string $name, array $data = []): string
    {
        return $this->make($name)->render($data);
    }

    /**
     * Create a new template.
     * @param string $name
     * @return Template
     */
    public function make(string $name): Template
    {
        return new Template($this, $name);
    }
}
