<?php

namespace Jinya\Plates\Extension;

use JetBrains\PhpStorm\Pure;
use Jinya\Plates\Engine;
use LogicException;

/**
 * Extension that adds the ability to create "cache busted" asset URLs.
 */
class Asset extends BaseExtension
{
    /**
     * Create new Asset instance.
     * @param string $path Path to asset directory.
     * @param bool $filenameMethod Enables the filename method.
     */
    #[Pure]
    public function __construct(public string $path, public bool $filenameMethod = false)
    {
        $this->path = rtrim($path, '/');
    }

    /**
     * Register extension function.
     */
    public function register(Engine $engine): void
    {
        $engine->functions->add('asset', [$this, 'cachedAssetUrl']);
    }

    /**
     * Create "cache busted" asset URL.
     */
    public function cachedAssetUrl(string $url): string
    {
        $filePath = $this->path . '/' . ltrim($url, '/');

        if (!file_exists($filePath)) {
            throw new LogicException(
                "Unable to locate the asset \"$url\" in the \"$this->path\" directory."
            );
        }

        $lastUpdated = filemtime($filePath);
        $pathInfo = pathinfo($url);
        $filename = $pathInfo['filename'];
        $dirname = $pathInfo['dirname'] ?? '';
        $extension = $pathInfo['extension'] ?? '';

        if ($dirname === '.') {
            $directory = '';
        } elseif ($dirname === DIRECTORY_SEPARATOR) {
            $directory = '/';
        } else {
            $directory = "$dirname/";
        }

        if ($this->filenameMethod) {
            return "$directory$filename.$lastUpdated.$extension";
        }

        return "$directory$filename.$extension?v=$lastUpdated";
    }
}
