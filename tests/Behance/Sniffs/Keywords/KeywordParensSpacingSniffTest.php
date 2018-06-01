<?php

class Behance_Sniffs_Keywords_KeywordParensSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList($testFile) {

    return [
      4 => 1,
      5 => 1,
      6 => 1,
      7 => 2,
      10 => 1,
      11 => 1,
      12 => 1,
      13 => 2,
      15 => 1,
      16 => 1,
      17 => 1,
      18 => 2,
      21 => 1,
      22 => 1,
      23 => 1,
      24 => 2,
      27 => 1,
      28 => 1,
      29 => 1,
      30 => 2,
      32 => 1,
      33 => 1,
      34 => 1,
      35 => 2,
      37 => 1,
      38 => 1,
      39 => 1,
      40 => 2,
      44 => 1,
      45 => 1,
      46 => 1,
      47 => 2,
      50 => 1,
      51 => 1,
      52 => 1,
      53 => 2,
      56 => 1,
      57 => 1,
      58 => 1,
      59 => 2,
      61 => 1,
      62 => 1,
      63 => 1,
      64 => 2,
      66 => 1,
      67 => 1,
      68 => 1,
      69 => 2,
      71 => 1,
      72 => 1,
      73 => 1,
      74 => 2,
      76 => 1,
      77 => 1,
      78 => 1,
      79 => 2,
      81 => 1,
      82 => 1,
      83 => 1,
      84 => 2,
      86 => 1,
      87 => 1,
      88 => 1,
      89 => 1
    ];

  } // getErrorList

  public function getWarningList($testFile) {

    return [];

  } // getWarningList

} // Behance_Sniffs_Keywords_KeywordParensSpacingSniffTest
