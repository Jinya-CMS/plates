# Extensions

Creating extensions couldn't be easier, and can really make Plates sing for your specific project. Start by creating a
class that implements `\Jinya\Plates\Extension\ExtensionInterface`. Next, register your
template [functions](functions.md) within a `register()` method.

## Simple extensions example

```php
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\ExtensionInterface;

class ChangeCase implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('uppercase', [$this, 'uppercaseString']);
        $engine->registerFunction('lowercase', [$this, 'lowercaseString']);
    }

    public function uppercaseString($var)
    {
        return strtoupper($var);
    }

    public function lowercaseString($var)
    {
        return strtolower($var);
    }
}
```

To use this extension in your template, simply call your new functions:

```php
<p>Hello, <?= $this->e($this->uppercase($name)) ?></p>
```

They can also be used in a [batch](../templates/functions.md) compatible function:

```php
<h1>Hello <?= $this->e($name, 'uppercase') ?></h1>
```

## Single method extensions

Alternatively, you may choose to expose the entire extension object to the template using a single function. This can
make your templates more legible and also reduce the chance of conflicts with other extensions.

```php
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\ExtensionInterface;

class ChangeCase implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('case', [$this, 'getObject']);
    }

    public function getObject()
    {
        return $this;
    }

    public function upper(string $var)
    {
        return strtoupper($var);
    }

    public function lower(string $var)
    {
        return strtolower($var);
    }
}
```

To use this extension in your template, first call the primary function, then the secondary functions:

```php
<p>Hello, <?= $this->e($this->case()->upper($name)) ?></p>
```

## Loading extensions

To enable an extension, load it into the [engine](index.md) object using the `loadExtension()` method.

```php
$engine->loadExtension(new ChangeCase());
```

## Accessing the engine

It may be desirable to access the `engine` object from within your extension. Plates makes this object available to you.
The engine is automatically passed to the `register()` method.

```php
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\ExtensionInterface;

class MyExtension implements ExtensionInterface
{
    protected Engine $engine;

    public function register(Engine $engine)
    {
        $this->engine = $engine;

        // Register functions
        // ...
    }
}
```
