<?php

namespace League\Plates\Template;

use League\Plates\Extension\ExtensionInterface;
use LogicException;

/**
 * A template function.
 */
class Func
{
    /**
     * The function name.
     */
    protected string $name;

    /**
     * The function callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Create new Func instance.
     */
    public function __construct(string $name, callable $callback)
    {
        $this->setName($name);
        $this->setCallback($callback);
    }

    /**
     * Get the function name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the function name.
     */
    public function setName(string $name): Func
    {
        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name) !== 1) {
            throw new LogicException('Not a valid function name.');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Get the function callback.
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Set the function callback
     */
    public function setCallback(?callable $callback): Func
    {
        if (! is_callable($callback, true)) {
            throw new LogicException('Not a valid function callback.');
        }

        $this->callback = $callback;

        return $this;
    }

    /**
     * Call the function.
     */
    public function call(Template $template = null, array $arguments = []): mixed
    {
        if (is_array($this->callback) && isset($this->callback[0]) && $this->callback[0] instanceof ExtensionInterface
        ) {
            $this->callback[0]->template = $template;
        }

        return call_user_func_array($this->callback, $arguments);
    }
}
