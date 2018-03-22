<?php

namespace PixelUnion\Tests;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Config;

class BaseSniffTestCase extends TestCase
{
    private function prepareLocalFileForSniffs(string $sniffFile, string $fixtureFile): LocalFile
    {
        $config = new Config();
        $ruleset = new Ruleset($config);

        $ruleset->registerSniffs([$sniffFile], [], []);
        $ruleset->populateTokenListeners();
        return new LocalFile($fixtureFile, $ruleset, $config);
    }

    private function getLineNumbersFromMessages(array $messages): array
    {
        return array_keys($messages);
    }

    private function getWarningLineNumbersFromFile(LocalFile $phpcsFile): array
    {
        return $this->getLineNumbersFromMessages($phpcsFile->getWarnings());
    }

    private function getErrorLineNumbersFromFile(LocalFile $phpcsFile): array
    {
        return $this->getLineNumbersFromMessages($phpcsFile->getErrors());
    }

    public function getLinesForSniff()
    {
        // get path to test
        $currentTestPath = str_replace('\\', '/', get_called_class());

        // get path to sniff associated with test
        $targetSniffPath = str_replace(
            ['Tests', 'Test'],
            ['Sniffs', ''],
            $currentTestPath
        );

        $fixtureFile = __DIR__ . '/../../' . $currentTestPath . '.inc';
        $sniffFile = __DIR__ . '/../../' . $targetSniffPath . '.php';
        $phpcsFile = $this->prepareLocalFileForSniffs($sniffFile, $fixtureFile);
        $phpcsFile->process();

        return [
          'errors' => $this->getErrorLineNumbersFromFile($phpcsFile),
          'warnings' => $this->getWarningLineNumbersFromFile($phpcsFile)
        ];
    }
}
