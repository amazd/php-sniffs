<?php

class Behance_Sniffs_Keywords_KeywordParensSpacingSniffTest extends AbstractSniffUnitTest {

  public function getErrorList() {

    return [
        4  => 1,
        5  => 1,
        6  => 1,
        10 => 1,
        11 => 1,
        12 => 1,
        15 => 1,
        16 => 1,
        17 => 1,
        21 => 1,
        22 => 1,
        23 => 1,
        27 => 1,
        28 => 1,
        29 => 1,
        32 => 1,
        33 => 1,
        34 => 1,
        37 => 1,
        38 => 1,
        39 => 1,
        44 => 1,
        45 => 1,
        46 => 1,
        50 => 1,
        51 => 1,
        52 => 1,
        56 => 1,
        57 => 1,
        58 => 1,
        61 => 1,
        62 => 1,
        63 => 1,
        66 => 1,
        67 => 1,
        68 => 1,
        71 => 1,
        72 => 1,
        73 => 1,
        76 => 1,
        77 => 1,
        78 => 1
    ];

  } // getErrorList

  public function getWarningList() {

    return [];

  } // getWarningList

} // Behance_Sniffs_Keywords_KeywordParensSpacingSniffTest
