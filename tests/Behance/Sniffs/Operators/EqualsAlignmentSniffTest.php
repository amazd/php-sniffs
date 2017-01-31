<?php

class Behance_Sniffs_Operators_EqualsAlignmentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        3    => 1,
        24   => 1,
        27   => 1,
        29   => 1,
        31   => 1,
        33   => 1,
        34   => 1,
        76   => 1,
        78   => 1,
        80   => 1,
        82   => 1,
        90   => 1,
        92   => 1,
        93   => 1,
        100  => 1,
        109  => 1,
        113  => 1,
        114  => 1,
        153  => 1,
        154  => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Operators_EqualsAlignmentSniffTest
