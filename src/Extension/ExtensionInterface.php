<?php

namespace Jinya\Plates\Extension;

use Jinya\Plates\Engine;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine): void;
}
