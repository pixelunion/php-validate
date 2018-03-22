# PHP Validate

A composer package that provides php validation in accordance with PRS-2
standards, along with any extra validation rules we choose.

## Installation

This is a composer package but is hosted on github.

Add this to `composer.json`:
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pixelunion/pxu-php-format"
        }
    ],

```

And then this to `require-dev`:
```
        "pixelunion/pxu-php-format": "dev-master"
```

And then run `composer install` as per usual.

## Usage: CLI

```
$ vendor/bin/php-validate <file>
```

Runs [PHP_Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) on the
given file(s).

Leaving blank will run against all files in a project.

## Usage: Manual

To use our shared ruleset manually, point your `--standard` argument to
`vendor/pixelunion/pxu-php-format/PixelUnion/ruleset.xml`

## Usage with Travis CI

For Travis builds we want to run this only within a given commit range (to
iteratively bring existing code up to a new standard), and only if PHP files
exist within this commit range. We have a [bash script](https://gist.github.com/essmahr/05f37b0dec779c5d01b9225241e7d208)
which gets this done.

TBD: integrating the above script into projects more easily :smiley:

Then add this to your project's Travis config:

```yaml
script:
  - "if [ \"$TRAVIS_PULL_REQUEST\" != \"false\" ]; then ./travis/validate $TRAVIS_COMMIT_RANGE; fi"
  - ...
  - ...
```

## Custom Sniffs

Extra sniffs beyond the PSR-2 standard can be added in `PixelUnion/Sniffs`.
Documentation for writing custom sniffs can be found in the
[PHP_Codesniffer Wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Coding-Standard-Tutorial#creating-the-sniff).
Please organize custom sniffs under a category, for example `Sniffs/Operators/TernaryOperatorSniff.php`.

### Testing custom sniffs

Sniff tests live in `PixelUnion/Tests`. From there they should share a directory
structure matching their location in `Sniffs`. So a sniff that exists in
`Sniffs/Category/SomeSniff.php` should have a test that lives in
`Tests/Category/SomeSniffTest.php`.

The test suite can be run with `composer run-script test`.

#### Sniff Fixtures

Each sniff test should also include a fixture, which is the PHP file that will
be sniffed against the given sniff rule. Fixtures should live next to their test
file with a `.inc` extension. So if you have a test at
`Tests/Category/SomeSniffTest.php` you're required to also include
`Tests/Category/SomeSniffTest.inc`.

#### Test Structure

At this point, all test asserts is what lines the errors and warnings should be
thrown on.

Test classes should extend the `BaseSniffTestCase` class, which makes a
`getLinesForSniff` method available. This method runs the sniff and returns an
array containing the line numbers of each error and warning. Simply assert that
the errors/warnings are thrown on the correct lines of the fixture provided.

#### Testing example

Let's say we have a sniff that disallows a `__set` method, like so:

```php
<?php

namespace PixelUnion\Sniffs\Methods;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMagicSetSniff implements Sniff {
    public function register() {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr) {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);
        if ($functionName === '__set') {
            $error = 'Magic setters are not allowed';
            $phpcsFile->addError($error, $stackPtr, 'MagicSet');
        }
    }
}
```

We'd write a fixture like this:
```php
<?php
class MyClass {
    public function __set($name, $value) {
        $this->$name = $value;
    }
}
```

And assert that it would fail on line 3 with a test like this:
```php
<?php

namespace PixelUnion\Tests\Methods;

use PixelUnion\Tests\BaseSniffTestCase;

class MagicMethodSniffTest extends BaseSniffTestCase
{
    public function testDisallowMagicSetSniff()
    {
        $lines = $this->getLinesForSniff();
        $this->assertEquals([3], $lines['errors']);
    }
}
```

Kudos to [this blog post](https://payton.codes/2017/12/15/creating-sniffs-for-a-phpcs-standard/#writing-tests)
for outlining an approach for testing custom sniffs.
