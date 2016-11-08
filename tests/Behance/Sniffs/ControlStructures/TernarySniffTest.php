<?php

class Behance_Sniffs_ControlStructures_TernarySniffTest extends AbstractSniffUnitTest {

  public function getErrorList( $testFile ) {

    return [
        10  => 1,
        15  => 1,
        20  => 1,
        23  => 1,
        29  => 1,
        31  => 1,
        32  => 1,
        37  => 1,
        39  => 2,
        41  => 1,
        43  => 1,
        45  => 1,
        47  => 1,
        49  => 1,
        51  => 1,
        53  => 1,
        55  => 1,
        61  => 1,
        62  => 1,
        66  => 1,
        69  => 1,
        73  => 1,
        78  => 1,
        81  => 1,
        82  => 1,
        86  => 1,
        91  => 1,
        94  => 1,
        99  => 1,
        104 => 1,
        112 => 1,
        116 => 1,
        117 => 1,
        118 => 1,
        119 => 1,
        122 => 1,
        124 => 1,
        129 => 1,
        197 => 1,
        202 => 1,
        205 => 1,
        206 => 1,
        217 => 1,
    ];

  } // getErrorList

  public function getWarningList( $testFile ) {

    return [];

  } // getWarningList

} // Behance_Sniffs_ControlStructures_TernarySniffTest
