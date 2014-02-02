<?php

class Behance_Sniffs_Classes_ClassNamingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [];

  } // getErrorList

  public function getWarningList() {

    return [
        3 => 1,
        6 => 1
    ];

  } // getWarningList

} // Behance_Sniffs_Classes_ClassNamingSniffTest
