<?php

namespace Jinya\Plates\Extension;

use Jinya\Plates\Engine;
use Jinya\Plates\Template\Template;

abstract class BaseExtension implements ExtensionInterface
{
    abstract public function register(Engine $engine): void;
}
