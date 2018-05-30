<?php

class Behance_Sniffs_Arrays_ArrayDeclarationSniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
      2   => 1,
      4   => 1,
      5   => 1,
      6   => 2,
      16  => 1,
      17  => 1,
      20  => 1,
      21  => 1,
      22  => 2,
      24  => 1,
      26  => 1,
      27  => 1,
      28  => 2,
      31  => 1,
      32  => 1,
      33  => 2,
      36  => 1,
      37  => 1,
      38  => 2,
      49  => 1,
      50  => 1,
      52  => 1,
      53  => 1,
      54  => 2,
      62  => 1,
      63  => 1,
      65  => 1,
      66  => 2,
      71  => 1,
      74  => 1,
      79  => 1,
      89  => 1,
      95  => 1,
      99  => 1,
      118 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Arrays_ArrayDeclarationSniffTest
