<?php

namespace Jinya\Plates\Template;

use Exception;
use JetBrains\PhpStorm\Deprecated;
use Jinya\Plates\Engine;
use LogicException;
use Throwable;

/**
 * Container which holds template data and provides access to template functions.
 */
class Template
{
    private Name $name;

    /**
     * @var array<array-key, mixed>
     */
    private array $data = [];

    /**
     * @var array<string, string>
     */
    private array $sections = [];

    private string|null $sectionName = null;

    private bool $appendSection = false;

    private string $layoutName;

    /**
     * @var array<array-key, mixed>
     */
    private array $layoutData;

    /**
     * Create new Template instance.
     */
    public function __construct(private readonly Engine $engine, string $name)
    {
        $this->name = new Name($engine, $name);

        $this->data($this->engine->data->get($name));
    }

    /**
     * Assign or get template data.
     * @param array<array-key, mixed> $data
     */
    public function data(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Get the template path.
     */
    public function path(): string
    {
        return $this->name->getPath();
    }

    /**
     * Magic method used to call extension functions.
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->engine->functions->get($name)->call($arguments);
    }

    /**
     * Alias for render() method.
     *
     * @throws Throwable
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Render the template and layout.
     *
     * @param array<array-key, mixed> $data
     * @throws Throwable
     */
    public function render(array $data = []): string
    {
        $this->data($data);
        extract($this->data);
        $path = $this->path();

        if (!$this->exists()) {
            $name = $this->name->name;
            throw new LogicException("The template \"$name\" could not be found at \"$path\".");
        }

        $level = ob_get_level();
        try {
            ob_start();

            include $path;

            $content = ob_get_clean() ?: '';

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
                $layout->sections = array_merge($this->sections, ['content' => $content]);
                $content = $layout->render($this->layoutData);
            }

            return $content ?: '';
        } catch (Throwable|Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Check if the template exists.
     */
    public function exists(): bool
    {
        return $this->name->doesPathExist();
    }

    /**
     * Set the template's layout.
     * @param array<array-key, mixed> $data
     */
    public function layout(string $name, array $data = []): void
    {
        $this->layoutName = $name;
        $this->layoutData = $data;
    }

    /**
     * Start a new append section block.
     */
    public function push(string $name): void
    {
        $this->appendSection = true;

        $this->start($name);
    }

    /**
     * Start a new section block.
     */
    public function start(string $name): void
    {
        if ($name === 'content') {
            throw new LogicException('The section name "content" is reserved.');
        }

        if ($this->sectionName !== null) {
            throw new LogicException('You cannot nest sections within other sections.');
        }

        $this->sectionName = $name;

        ob_start();
    }

    /**
     * Alias of start().
     * @deprecated
     */
    #[Deprecated]
    public function begin(string $name): void
    {
        $this->start($name);
    }

    /**
     * Alias of stop().
     * @deprecated
     */
    #[Deprecated]
    public function end(): void
    {
        $this->stop();
    }

    /**
     * Stop the current section block.
     */
    public function stop(): void
    {
        if (is_null($this->sectionName)) {
            throw new LogicException(
                'You must start a section before you can stop it.'
            );
        }

        if (!isset($this->sections[$this->sectionName])) {
            $this->sections[$this->sectionName] = '';
        }

        $ob = ob_get_clean() ?: '';

        $this->sections[$this->sectionName] = $this->appendSection ? $this->sections[$this->sectionName] . $ob : $ob;
        $this->sectionName = null;
        $this->appendSection = false;
    }

    /**
     * Returns the content for a section block.
     *
     * @param string $name Section name
     * @param string|null $default Default section content
     */
    public function section(string $name, string|null $default = null): string|null
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Fetch a rendered template.
     *
     * @param array<array-key, mixed> $data
     * @throws Throwable
     */
    public function fetch(string $name, array $data = []): string
    {
        return $this->engine->render($name, $data);
    }

    /**
     * Output a rendered template.
     *
     * @param array<array-key, mixed> $data
     * @throws Throwable
     */
    public function insert(string $name, array $data = []): void
    {
        echo $this->engine->render($name, $data);
    }

    /**
     * Alias to escape function.
     */
    public function e(string $string, string|null $functions = null): string
    {
        return $this->escape($string, $functions);
    }

    /**
     * Escape string.
     */
    public function escape(string $string, string|null $functions = null): string
    {
        static $flags;

        if (!isset($flags)) {
            $flags = ENT_QUOTES | ENT_SUBSTITUTE;
        }

        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string, flags: $flags);
    }

    /**
     * Apply multiple functions to variable.
     */
    public function batch(mixed $var, string $functions): string
    {
        foreach (explode('|', $functions) as $function) {
            if ($this->engine->functions->exists($function)) {
                $var = $this->$function($var);
            } elseif (is_callable($function)) {
                $var = $function($var);
            } else {
                throw new LogicException("The batch function could not find the \"$function\" function.");
            }
        }

        return $var;
    }
}
