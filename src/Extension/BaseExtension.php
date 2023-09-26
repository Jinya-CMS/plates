<?php

namespace League\Plates\Extension;

use League\Plates\Engine;
use League\Plates\Template\Template;

abstract class BaseExtension implements ExtensionInterface
{
    public Template $template;

    abstract public function register(Engine $engine): void;
}
