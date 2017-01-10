<?php

class Behance_Sniffs_Functions_FunctionMbStringSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        2    => 1,
        3    => 1,
        4    => 1,
        5    => 1,
        6    => 1,
        7    => 1,
        8    => 1,
        9    => 1,
        10    => 1,
        11    => 1,
        12    => 1,
        13    => 1,
        14    => 1,
        15    => 1,
        16    => 1,
        17    => 1,
        18    => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Functions_FunctionMbStringSniffTest
