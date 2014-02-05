<?php

class Behance_Sniffs_Functions_FunctionDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        8  => 1,
        16 => 1,
        17 => 1,
        42 => 1,
        46 => 2,
        49 => 1,
        51 => 1,
        52 => 1,
        53 => 1,
        54 => 1,
        56 => 1,
        57 => 2,
        58 => 1,
        61 => 1,
        62 => 2,
    ];

  } // getErrorList

  public function getWarningList() {

    return [
        4 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionDeclarationSniffTest
