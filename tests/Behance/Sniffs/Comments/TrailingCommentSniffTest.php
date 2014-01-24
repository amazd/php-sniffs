<?php

class Behance_Sniffs_Comments_TrailingCommentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        22 => 1,
        25 => 1,
        28 => 1,
        31 => 1,
        35 => 1,
        43 => 1,
        52 => 1,
    ];

  } // getErrorList

  public function getWarningList() {

    return [
    ];

  } // getWarningList

} // Behance_Sniffs_Comments_TrailingCommentSniffTest
