<?php

namespace Jinya\Plates\Template;

use LogicException;

/**
 * A template function.
 * @internal
 */
class Func
{
    /**
     * The function name.
     */
    protected string $name;

    /**
     * Create new Func instance.
     * @param callable $callback The function callback.
     */
    public function __construct(/** @noinspection PhpMissingParamTypeInspection */ string $name, public $callback)
    {
        $this->setName($name);
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
     * Call the function.
     * @param array<mixed> $arguments
     */
    public function call(array $arguments = []): mixed
    {
        return call_user_func_array($this->callback, $arguments);
    }
}
