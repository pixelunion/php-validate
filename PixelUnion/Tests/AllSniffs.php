<?php

namespace PixelUnion\Tests;

use PHP_CodeSniffer\Autoload;
use PHPUnit\TextUI\TestRunner;
use PHPUnit\Framework\TestSuite;

class AllSniffs
{
    const FILENAME_END_FOR_TESTS = 'Test.php';

    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        TestRunner::run(self::suite());
    }

    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        $suite = new TestSuite('Pixel Union CodeSniffing Standard');

        $testsDir = __DIR__;

        $di = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testsDir));
        foreach ($di as $file) {
            if (substr_compare(
                $file,
                self::FILENAME_END_FOR_TESTS,
                -strlen(self::FILENAME_END_FOR_TESTS)
            ) !== 0) {
                continue;
            }

            $className = Autoload::loadFile($file->getPathname());

            $suite->addTestSuite($className);
        }

        return $suite;
    }
}
