<?php

class Behance_Sniffs_Comments_TrailingCommentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList($testFile) {

    return [
      3 => 1,
      25 => 1,
      28 => 1,
      31 => 1,
      35 => 1,
      43 => 1,
      52 => 1,
      60 => 1,
      68 => 1,
      72 => 1,
      80 => 1,
      90 => 1,
      105 => 1,
      113 => 1
    ];

  }

  public function getWarningList($testFile) {

    return [];

  }

}
