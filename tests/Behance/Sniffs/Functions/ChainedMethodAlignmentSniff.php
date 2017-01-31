<?php

class Behance_Sniffs_Functions_ChainedMethodAlignmentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        51   => 1,
        57   => 1,
        63   => 1,
        67   => 1,
        68   => 1,
        71   => 1,
        72   => 1,
        78   => 1,
        82   => 1,
        89   => 1,
        94   => 1,
        95   => 1,
        99   => 1,
        104  => 1,
        107  => 1,
        108  => 1,
        118  => 1,
        126  => 1,
        132  => 1,
        133  => 1,
        139  => 1,
        144  => 1,
        147  => 1,
        148  => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Functions_ChainedMethodAlignmentSniff
