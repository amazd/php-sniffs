<?php

class Behance_Sniffs_Functions_FunctionDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        7  => 1,
        15 => 1,
        16 => 1,
        41 => 2,
        42 => 1,
        44 => 1,
        48 => 2,
        51 => 1,
        53 => 1,
        54 => 1,
        55 => 1,
        56 => 1,
        58 => 1,
        59 => 2,
        60 => 1,
        63 => 1,
        64 => 2,
        70 => 2,
        82 => 1,
    ];

  } // getErrorList

  public function getWarningList() {

    return [
        4 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionDeclarationSniffTest
