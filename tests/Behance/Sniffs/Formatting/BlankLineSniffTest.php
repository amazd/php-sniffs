<?php

class Behance_Sniffs_Formatting_BlankLineSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        3  => 1,
        14 => 1,
        22 => 1,
        28 => 1,
        40 => 1,
        45 => 1,
        56 => 1,
        68 => 1,
        73 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Formatting_BlankLineSniffTest
