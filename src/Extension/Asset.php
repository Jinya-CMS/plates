<?php

namespace League\Plates\Extension;

use JetBrains\PhpStorm\Pure;
use League\Plates\Engine;
use League\Plates\Template\Template;
use LogicException;

/**
 * Extension that adds the ability to create "cache busted" asset URLs.
 */
class Asset implements ExtensionInterface
{
    /**
     * Instance of the current template.
     * @var Template
     */
    public Template $template;

    /**
     * Path to asset directory.
     * @var string
     */
    public string $path;

    /**
     * Enables the filename method.
     * @var bool
     */
    public bool $filenameMethod;

    /**
     * Create new Asset instance.
     * @param string $path
     * @param bool $filenameMethod
     */
    #[Pure] public function __construct(string $path, bool $filenameMethod = false)
    {
        $this->path = rtrim($path, '/');
        $this->filenameMethod = $filenameMethod;
    }

    /**
     * Register extension function.
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('asset', array($this, 'cachedAssetUrl'));
    }

    /**
     * Create "cache busted" asset URL.
     * @param string $url
     * @return string
     */
    public function cachedAssetUrl(string $url): string
    {
        $filePath = $this->path . '/' . ltrim($url, '/');

        if (!file_exists($filePath)) {
            throw new LogicException(
                'Unable to locate the asset "' . $url . '" in the "' . $this->path . '" directory.'
            );
        }

        $lastUpdated = filemtime($filePath);
        $pathInfo = pathinfo($url);

        if ($pathInfo['dirname'] === '.') {
            $directory = '';
        } elseif ($pathInfo['dirname'] === DIRECTORY_SEPARATOR) {
            $directory = '/';
        } else {
            $directory = $pathInfo['dirname'] . '/';
        }

        if ($this->filenameMethod) {
            return $directory . $pathInfo['filename'] . '.' . $lastUpdated . '.' . $pathInfo['extension'];
        }

        return $directory . $pathInfo['filename'] . '.' . $pathInfo['extension'] . '?v=' . $lastUpdated;
    }
}
