<?php

class Behance_Sniffs_Operators_OperatorSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        5  => 4,
        12 => 2,
        19 => 2,
        26 => 2,
        33 => 2,
        40 => 2,
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Operators_OperatorSpacingSniffTest
