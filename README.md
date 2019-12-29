# Support Helpers

Awesome support helpers for PHP web development.

## Thanking

- https://github.com/illuminate/support

## Installing

```shell
$ composer require codesinging/support -vvv
```

## Usage

### helpers

1. `load_config($file)`

### Arr

1. `accessible($value)`
1. `add($array, $key, $value)`
1. `divide($array)`
1. `dot($array, $prepend = '')`
1. `except($array, $keys)`
1. `exists($array, $key)`
1. `first($array, callable $callback = null, $default = null)`
1. `last($array, callable $callback = null, $default = null)`
1. `flatten($array, $depth = INF)`
1. `forget(&$array, $keys)`
1. `get($array, $key, $default = null)`
1. `has($array, $keys)`
1. `isAssoc(array $array)`
1. `only($array, $keys)`
1. `prepend($array, $value, $key = null)`
1. `pull(&$array, $key, $default = null)`
1. `query($array)`
1. `random($array, $number = null)`
1. `set(&$array, $key, $value)`
1. `shuffle($array, $seed = null)`
1. `where($array, callable $callback)`
1. `wrap($value)`

### Str

1. `after($subject, $search)`
1. `afterLast($subject, $search)`
1. `before($subject, $search)`
1. `beforeLast($subject, $search)`
1. `camel($value)`
1. `contains($haystack, $needles)`
1. `containsAll($haystack, array $needles)`
1. `endsWith($haystack, $needles)`
1. `finish($value, $cap)`
1. `kebab($value)`
1. `length($value, $encoding = null)`
1. `limit($value, $limit = 100, $end = '...')`
1. `lower($value)`
1. `random($length = 16)`
1. `replaceArray($search, array $replace, $subject)`
1. `replaceFirst($search, $replace, $subject)`
1. `replaceLast($search, $replace, $subject)`
1. `snake($value, $delimiter = '_')`
1. `start($value, $prefix)`
1. `startsWith($haystack, $needles)`
1. `studly($value)`
1. `substr($string, $start, $length = null)`
1. `title($value)`
1. `ucfirst($string)`
1. `upper($value)`
1. `words($value, $words = 100, $end = '...')`

### Repository

1. `has(string $key)`
1. `get($key, $default = null)`
1. `getMany(array $keys)`
1. `set($key, $value = null)`
1. `prepend(string $key, $value)`
1. `push(string $key, $value)`
1. `all()`

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/codesinging/support/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/codesinging/support/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT