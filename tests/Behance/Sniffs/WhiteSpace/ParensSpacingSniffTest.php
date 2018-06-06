<?php

class Behance_Sniffs_Whitespace_ParensSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList($testFile) {

    return [
      3 => 1,
      4 => 1,
      5 => 2,
    ];

  }

  public function getWarningList($testFile) {

    return [];

  }

}
