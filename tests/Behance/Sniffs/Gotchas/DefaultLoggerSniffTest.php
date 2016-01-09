<?php

class Behance_Sniffs_Gotchas_DefaultLoggerSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        4  => 1,
        8  => 1,
        12 => 1,
        16 => 1,
        20 => 1,
        24 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Gotchas_DefaultLoggerSniffTest
