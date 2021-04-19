# ImmutableReadFile - An immutable read-only file wrapper for PHP

[![Latest Stable Version](https://poser.pugx.org/mallardduck/immutable-read-file/v)](//packagist.org/packages/mallardduck/immutable-read-file)
[![Total Downloads](https://poser.pugx.org/mallardduck/immutable-read-file/downloads)](//packagist.org/packages/mallardduck/immutable-read-file)
[![Latest Unstable Version](https://poser.pugx.org/mallardduck/immutable-read-file/v/unstable)](//packagist.org/packages/mallardduck/immutable-read-file)
[![License](https://poser.pugx.org/mallardduck/immutable-read-file/license)](//packagist.org/packages/mallardduck/immutable-read-file)

If you've ever used `fopen`/`SplFileObject` and wanted the results to be idempotent<sup>1</sup> this is de way.

With this library you get a read-only immutable wrapper for the basic SplFileObject - which is essentially the OOP fopen.  

You probably would only rarely have a use case for this but if you do you'll know...and it may be hecking useful.

<sup>1 = Only technically because the wrapper tracks a "canonical" position to always work from.</sup>

## Installation

You can install the package via composer:

```bash
composer require mallardduck/immutable-read-file
```

## Usage

```php
use MallardDuck\ImmutableReadFile\ImmutableFile;

$fileName = __DIR__ . '/tests/stubs/json.txt'; // CONTENT: {"hello": "world"}

$step1 = ImmutableFile::fromFilePath($fileName);
echo $step1->fgetc(); // { - The first character of your file
echo $step1->fgetc(); // { - The first character of your file, again
$step2 = $step1->advanceBytePosition(); // A new r/o immutable entity advanced a single byte by default.
echo $step2->fgetc(); // " - The second character of your file
echo $step2->fgetc(); // " - The second character of your file, again
```

## Why! I don't get it?

So, you were warned that this would only be useful rarely - but when it is useful it's hecking useful.
You did read that right?  

The main way to understand when this is useful is to understand how it's different from `fopen` or `SplFileObject`.
Since this will be useful in cases when the default behavior of those is not desired.

So see for yourself! Examples...

### How `fopen` works by default
The use of a second `fopen`, and the `fseek`, are to emulate what `$step1->advanceBytePosition()` does in the usage example.
```php
$fileName = __DIR__ . '/tests/stubs/json.txt'; // CONTENT: {"hello": "world"}

$step1 = fopen($fileName, 'r');
echo fgetc($step1) . PHP_EOL; // { - The first character of your file
echo fgetc($step1) . PHP_EOL; // " - The second character of your file
$step2 = fopen($fileName, 'r');
fseek($step2, 1);
echo fgetc($step2) . PHP_EOL; // " - The second character of your file
echo fgetc($step2) . PHP_EOL; // h - The third character of your file
```

### How `SplFileObject` works by default
The use of a second `new SplFileObject`, and the `$step2->fseek(1)`, are to emulate what `$step1->advanceBytePosition()` does in the usage example.
```php
$fileName = __DIR__ . '/tests/stubs/json.txt'; // CONTENT: {"hello": "world"}

$step1 = new SplFileObject($fileName, 'r');
echo $step1->fgetc() . PHP_EOL; // { - The first character of your file
echo $step1->fgetc() . PHP_EOL; // " - The second character of your file
$step2 = new SplFileObject($fileName, 'r');
$step2->fseek(1);
echo $step2->fgetc() . PHP_EOL; // " - The second character of your file
echo $step2->fgetc() . PHP_EOL; // h - The third character of your file
```

### Summary

Comparing the ways that `fopen` and `SplFileObject` work we can see they are functionally identical to each other.
However, this also highlights how they are different from `ImmutableReadFile`.

When you use a method on either `fopen`/`SplFileObject` that returns content, then the current cursor position is incremented.
That means when you run `fgetc` on these you'll always get either: the next character, or the EOF.


However for `ImmutableReadFile`, these methods do not affect the cursor position. 
This means that if you run `fgetc`, you'll always get the same exact character.
Only until you explicitly advance the byte position will you get a novel character.
When that happens you're actually getting a new instance of `ImmutableFile` too.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dan Pock](https://github.com/mallardduck)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.