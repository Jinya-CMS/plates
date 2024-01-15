<?php

namespace Jinya\Plates\Template;

/**
 * A template folder.
 * @internal
 */
class Folder
{
    /**
     * Create a new Folder instance.
     *
     * @param string $path The folder path.
     * @param bool $fallback The folder fallback status.
     */
    public function __construct(public string $path, public bool $fallback = false)
    {
    }
}
