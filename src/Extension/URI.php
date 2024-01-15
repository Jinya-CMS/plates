<?php

namespace Jinya\Plates\Extension;

use JetBrains\PhpStorm\Pure;
use Jinya\Plates\Engine;

/**
 * Extension that adds a number of URI checks.
 */
class URI extends BaseExtension
{
    /**
     * The request URI as an array.
     * @var string[]
     */
    public array $parts;

    /**
     * Create new URI instance.
     * @param string $uri The request URI.
     */
    #[Pure]
    public function __construct(public string $uri)
    {
        $this->parts = explode('/', $this->uri);
    }

    /**
     * Register extension functions.
     */
    public function register(Engine $engine): void
    {
        $engine->functions->add('uri', [$this, 'runUri']);
    }

    /**
     * Perform URI check.
     */
    public function runUri(
        int|string|null $var1 = null,
        string|null $var2 = null,
        string|null $var3 = null,
        string|null $var4 = null
    ): mixed {
        if ($var1 === null) {
            return $this->uri;
        }

        if (is_int($var1)) {
            if ($var2 === null) {
                return $this->parts[$var1] ?? null;
            }

            return $this->checkUriSegmentMatch($var1, $var2, $var3, $var4);
        }

        return $this->checkUriRegexMatch($var1, $var2, $var3);
    }

    /**
     * Perform a URI segment match.
     */
    private function checkUriSegmentMatch(
        int $key,
        string $string,
        mixed $returnOnTrue = null,
        mixed $returnOnFalse = null
    ): mixed {
        if (array_key_exists($key, $this->parts) && $this->parts[$key] === $string) {
            return $returnOnTrue ?? true;
        }

        return $returnOnFalse ?? false;
    }

    /**
     * Perform a regular express match.
     */
    private function checkUriRegexMatch(string $regex, mixed $returnOnTrue = null, mixed $returnOnFalse = null): mixed
    {
        if (preg_match("#^$regex$#", $this->uri) === 1) {
            return $returnOnTrue ?? true;
        }

        return $returnOnFalse ?? false;
    }
}
