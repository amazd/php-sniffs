<?php

class Behance_Sniffs_ControlStructures_TernarySniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        10 => 1,
        11 => 1,
        15 => 1,
        18 => 1,
        22 => 1,
        27 => 1,
        30 => 1,
        31 => 1,
        44 => 1,
        49 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_ControlStructures_TernarySniffTest
