# PHP Validate

A composer package that provides php validation in accordance with PRS-2
standards, with configuration specific to Laravel.

## Installation

```
[coming soon]
```

## Usage: CLI

```
$ vendor/bin/php-validate <file>
```

Runs [PHP_Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer) on the
given file(s).

Leaving blank will run against all files in a project.

## Usage: Manual

To use our shared ruleset manually, point your `--standard` argument to
`vendor/pixelunion/php-validate/config/laravel_phpcs_ruleset.xml`

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


