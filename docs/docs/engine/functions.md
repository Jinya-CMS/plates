# Functions

While [extensions](extensions.md) are awesome for adding additional reusable functionality to Plates, sometimes it's
easier to just create a one-off function for a specific use case. Plates makes this easy to do.

## Registering functions

```php
// Create new Plates engine
$templates = new \Jinya\Plates\Engine('/path/to/templates');

// Register a one-off function
$templates->registerFunction('uppercase', fn (string $string) => strtoupper($string));
```

To use this function in a template, simply call it like any other function:

```php
<h1>Hello <?= $this->e($this->uppercase($name)) ?></h1>
```

It can also be used in a [batch](../templates/functions.md) compatible function:

```php
<h1>Hello <?= $this->e($name, 'uppercase') ?></h1>
```
