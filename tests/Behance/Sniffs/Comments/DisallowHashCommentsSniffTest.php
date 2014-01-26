<?php

class Behance_Sniffs_Comments_DisallowHashCommentsSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        3  => 1,
        7  => 1
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Comments_DisallowHashCommentsSniffTest
