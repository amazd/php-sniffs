<?php

class Behance_Sniffs_Functions_FunctionDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList($testFile) {

    return [
      7 => 1,
      15 => 1,
      16 => 1,
      42 => 1,
      43 => 1,
      48 => 1,
      49 => 2,
      50 => 1,
      52 => 1,
      59 => 2,
      60 => 1,
      61 => 1,
      64 => 1,
      65 => 1,
      71 => 2,
      83 => 1,
      85 => 1,
      86 => 1,
      87 => 1,
      88 => 1,
      89 => 1,
      107 => 1,
      111 => 1,
      119 => 1,
    ];

  }

  public function getWarningList($testFile) {

    return [
      4 => 1
    ];

  }

}
