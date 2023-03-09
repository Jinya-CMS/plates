<?php

namespace League\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Template\Template;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    /**
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void;
}
