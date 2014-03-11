<?php

class Behance_Sniffs_Arrays_ArrayDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        2 => 1,
        4 => 1,
        5 => 1,
        6 => 1
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Arrays_ArrayDeclarationSniffTest
