<?php

class Behance_Sniffs_Classes_ClassDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        3  => 1,
        6  => 1,
        9  => 1
    ];

  } // getErrorList

  public function getWarningList() {

    return [
        13 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Classes_ClassDeclarationSniffTest
