<?php

class Behance_Sniffs_Arrays_ArrayBracketSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        4 => 2,
        9 => 1,
        12 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Arrays_ArrayBracketSpacingSniffTest
