<?php

class Behance_Sniffs_Operators_OperatorSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        5  => 4,
        9  => 2,
        13 => 2,
        17 => 2,
        21 => 2,
        25 => 2,
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Operators_OperatorSpacingSniffTest
