<?php

namespace League\Plates\Extension;

use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use LogicException;

/**
 * Extension that adds the ability to create "cache busted" asset URLs.
 */
class Asset extends BaseExtension
{
    /**
     * Path to asset directory.
     */
    public string $path;

    /**
     * Enables the filename method.
     */
    public bool $filenameMethod;

    /**
     * Create new Asset instance.
     */
    #[Pure]
    public function __construct(string $path, bool $filenameMethod = false)
    {
        $this->path = rtrim($path, '/');
        $this->filenameMethod = $filenameMethod;
    }

    /**
     * Register extension function.
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('asset', [$this, 'cachedAssetUrl']);
    }

    /**
     * Create "cache busted" asset URL.
     */
    public function cachedAssetUrl(string $url): string
    {
        $filePath = $this->path.'/'.ltrim($url, '/');

        if (! file_exists($filePath)) {
            throw new LogicException(
                'Unable to locate the asset "'.$url.'" in the "'.$this->path.'" directory.'
            );
        }

        $lastUpdated = filemtime($filePath);
        $pathInfo = pathinfo($url);

        if ($pathInfo['dirname'] === '.') {
            $directory = '';
        } elseif ($pathInfo['dirname'] === DIRECTORY_SEPARATOR) {
            $directory = '/';
        } else {
            $directory = $pathInfo['dirname'].'/';
        }

        if ($this->filenameMethod) {
            return $directory.$pathInfo['filename'].'.'.$lastUpdated.'.'.$pathInfo['extension'];
        }

        return $directory.$pathInfo['filename'].'.'.$pathInfo['extension'].'?v='.$lastUpdated;
    }
}
