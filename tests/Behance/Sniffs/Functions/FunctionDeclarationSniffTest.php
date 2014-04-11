<?php

class Behance_Sniffs_Functions_FunctionDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        9  => 1,
        17 => 1,
        18 => 1,
        43 => 1,
        47 => 2,
        50 => 1,
        52 => 1,
        53 => 1,
        54 => 1,
        55 => 1,
        57 => 1,
        58 => 2,
        59 => 1,
        62 => 1,
        63 => 2,
    ];

  } // getErrorList

  public function getWarningList() {

    return [
        5 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionDeclarationSniffTest
