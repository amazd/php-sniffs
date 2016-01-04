<?php

class Behance_Sniffs_Keywords_OneUsePerStatementSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        6  => 1,
        8  => 1,
        12 => 1,
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Keywords_OneUsePerStatementSniffTest
