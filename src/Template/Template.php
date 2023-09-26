<?php

namespace League\Plates\Template;

use Exception;
use League\Plates\Engine;
use LogicException;
use Throwable;

/**
 * Container which holds template data and provides access to template functions.
 */
class Template
{
    /**
     * Instance of the template engine.
     */
    protected Engine $engine;

    /**
     * The name of the template.
     */
    protected Name $name;

    /**
     * The data assigned to the template.
     */
    protected array $data = [];

    /**
     * An array of section content.
     */
    protected array $sections = [];

    /**
     * The name of the section currently being rendered.
     */
    protected ?string $sectionName = null;

    /**
     * Whether the section should be appended or not.
     */
    protected bool $appendSection = false;

    /**
     * The name of the template layout.
     */
    protected string $layoutName;

    /**
     * The data assigned to the template layout.
     */
    protected array $layoutData;

    /**
     * Create new Template instance.
     */
    public function __construct(Engine $engine, string $name)
    {
        $this->engine = $engine;
        $this->name = new Name($engine, $name);

        $this->data($this->engine->getData($name));
    }

    /**
     * Assign or get template data.
     */
    public function data(array $data = null): ?array
    {
        if (is_null($data)) {
            return $this->data;
        }

        $this->data = array_merge($this->data, $data);

        return null;
    }

    /**
     * Magic method used to call extension functions.
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->engine->getFunction($name)->call($this, $arguments);
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
     * @throws Throwable
     */
    public function render(array $data = []): ?string
    {
        $this->data($data);
        extract($this->data, EXTR_OVERWRITE);

        if (! $this->exists()) {
            throw new LogicException(
                "The template \"{$this->name->getName()}\" could not be found at \"{$this->path()}\"."
            );
        }

        $level = ob_get_level();
        try {
            ob_start();

            include $this->path();

            $content = ob_get_clean();

            if (isset($this->layoutName)) {
                $layout = $this->engine->make($this->layoutName);
                $layout->sections = array_merge($this->sections, ['content' => $content]);
                $content = $layout->render($this->layoutData);
            }

            return $content;
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
     * Get the template path.
     */
    public function path(): string
    {
        return $this->name->getPath();
    }

    /**
     * Set the template's layout.
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

        if (isset($this->sectionName) && $this->sectionName !== null) {
            throw new LogicException('You cannot nest sections within other sections.');
        }

        $this->sectionName = $name;

        ob_start();
    }

    /**
     * Alias of start().
     */
    public function begin(string $name): void
    {
        $this->start($name);
    }

    /**
     * Alias of stop().
     */
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

        if (! isset($this->sections[$this->sectionName])) {
            $this->sections[$this->sectionName] = '';
        }

        $this->sections[$this->sectionName] = $this->appendSection ? $this->sections[$this->sectionName].ob_get_clean(
        ) : ob_get_clean();
        $this->sectionName = null;
        $this->appendSection = false;
    }

    /**
     * Returns the content for a section block.
     *
     * @param  string  $name Section name
     * @param  string|null  $default Default section content
     */
    public function section(string $name, string $default = null): ?string
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Fetch a rendered template.
     *
     * @throws Throwable
     * @throws Throwable
     * @throws Throwable
     */
    public function fetch(string $name, array $data = []): string
    {
        return $this->engine->render($name, $data);
    }

    /**
     * Output a rendered template.
     *
     * @throws Throwable
     * @throws Throwable
     * @throws Throwable
     */
    public function insert(string $name, array $data = []): void
    {
        echo $this->engine->render($name, $data);
    }

    /**
     * Alias to escape function.
     */
    public function e(string $string, string $functions = null): string
    {
        return $this->escape($string, $functions);
    }

    /**
     * Escape string.
     */
    public function escape(string $string, string $functions = null): string
    {
        static $flags;

        if (! isset($flags)) {
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
    public function batch(mixed $var, string $functions): mixed
    {
        foreach (explode('|', $functions) as $function) {
            if ($this->engine->doesFunctionExist($function)) {
                $var = $this->$function($var);
            } elseif (is_callable($function)) {
                $var = $function($var);
            } else {
                throw new LogicException("The batch function could not find the \"{$function}\" function.");
            }
        }

        return $var;
    }
}
