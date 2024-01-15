<?php

namespace Jinya\Plates\Extension;

use Jinya\Plates\Engine;
use Jinya\Plates\Template\Template;

abstract class BaseExtension implements ExtensionInterface
{
    public Template $template;

    abstract public function register(Engine $engine): void;
}
