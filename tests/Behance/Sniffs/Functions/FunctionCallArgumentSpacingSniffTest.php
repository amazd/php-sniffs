<?php

class Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        2 => 1,
        3 => 1,
        6 => 1
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniffTest
