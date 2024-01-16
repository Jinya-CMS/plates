<?php

namespace Jinya\Plates\Extension;

use Jinya\Plates\Engine;

abstract class BaseExtension implements ExtensionInterface
{
    abstract public function register(Engine $engine): void;
}
