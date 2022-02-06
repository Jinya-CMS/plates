<?php

namespace League\Plates\Extension;

use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use League\Plates\Template\Template;
use LogicException;

/**
 * Extension that adds a number of URI checks.
 */
class URI implements ExtensionInterface
{
    /**
     * Instance of the current template.
     * @var Template
     */
    public Template $template;

    /**
     * The request URI.
     * @var string
     */
    protected string $uri;

    /**
     * The request URI as an array.
     * @var array|false
     */
    protected false|array $parts;

    /**
     * Create new URI instance.
     * @param string $uri
     */
    #[Pure] public function __construct(string $uri)
    {
        $this->uri = $uri;
        $this->parts = explode('/', $this->uri);
    }

    /**
     * Register extension functions.
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('uri', array($this, 'runUri'));
    }

    /**
     * Perform URI check.
     * @param int|string|array|null $var1
     * @param mixed $var2
     * @param mixed $var3
     * @param mixed $var4
     * @return mixed
     */
    public function runUri(
        null|int|string|array $var1 = null,
        mixed $var2 = null,
        mixed $var3 = null,
        mixed $var4 = null
    ): mixed {
        if (is_null($var1)) {
            return $this->uri;
        }

        if (is_numeric($var1) && is_null($var2)) {
            return $this->parts[$var1] ?? null;
        }

        if (is_numeric($var1) && is_string($var2)) {
            return $this->checkUriSegmentMatch($var1, $var2, $var3, $var4);
        }

        if (is_string($var1)) {
            return $this->checkUriRegexMatch($var1, $var2, $var3);
        }

        throw new LogicException('Invalid use of the uri function.');
    }

    /**
     * Perform a URI segment match.
     * @param integer $key
     * @param string $string
     * @param mixed $returnOnTrue
     * @param mixed $returnOnFalse
     * @return mixed
     */
    protected function checkUriSegmentMatch(
        int $key,
        string $string,
        mixed $returnOnTrue = null,
        mixed $returnOnFalse = null
    ): mixed {
        if (array_key_exists($key, $this->parts) && $this->parts[$key] === $string) {
            return is_null($returnOnTrue) ? true : $returnOnTrue;
        }

        return is_null($returnOnFalse) ? false : $returnOnFalse;
    }

    /**
     * Perform a regular express match.
     * @param string $regex
     * @param mixed $returnOnTrue
     * @param mixed $returnOnFalse
     * @return mixed
     */
    protected function checkUriRegexMatch(string $regex, mixed $returnOnTrue = null, mixed $returnOnFalse = null): mixed
    {
        if (preg_match('#^' . $regex . '$#', $this->uri) === 1) {
            return is_null($returnOnTrue) ? true : $returnOnTrue;
        }

        return is_null($returnOnFalse) ? false : $returnOnFalse;
    }
}
