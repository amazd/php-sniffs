<?php
$misalignedArr = [1,
    2,
  3,
4,
5];

$goodArr = [
    1,
    2,
    3,
    4,
    5
];

$oneLineMissingSpaceBeforeDoubleArrow = [ 'key1'=> $val ];
$oneLineMissingSpaceAfterDoubleArrow  = [ 'key2' =>$val ];
$oneLineGoodDoubleArrow               = [ 'key3' => $val ];

$oneLineExtraSpaceBeforeComma  = [ 'val' , 'val' ];
$oneLineMissingSpaceAfterComma = [ 'val','val' ];
$oneLinebackwardsCommaSpacing  = [ 'val' ,'val' ];
$oneLineGoodComma              = [ 'val', 'val' ];
$oneLineGoodCommaExtraSpaces   = [ 'val',           'val' ];

$oneLineMissingSpaceAfterOpen = ['val' ];
$oneLineMissingSpaceBeforeClose = [ 'val'];
$oneLineMissingSpaceAround = ['val'];
$oneLineGoodSpacing = [ 'val' ];

$oneLineMissingSpaceAfterOpen = ['key' => 'val' ];
$oneLineMissingSpaceBeforeClose = [ 'key' => 'val'];
$oneLineMissingSpaceAround = ['key' => 'val'];
$oneLineGoodSpacing = [ 'key' => 'val' ];

$oneLineMissingSpaceAfterOpen = ['val', 'val2' ];
$oneLineMissingSpaceBeforeClose = [ 'val', 'val2'];
$oneLineMissingSpaceAround = ['val', 'val2'];
$oneLineGoodSpacing = [ 'val', 'val2' ];

$goodArrWithFunctionValuesWithArrParams = [
    $this->something( [ 'key' => 'val' ] ),
    $this->something( [ 'key' => 'val' ] ),
    $this->something( [ 'key' => 'val' ] ),
];

$misalignedNestedArr = [
    [ 'a' => 'b' ],
      [ 'a' => 'b' ],
    [1,
        2,
      3,
    4,
    5]
];

$goodArrWithConcatenatedKeys = [
    $key . '_val'  => 'a',
    $key . '_val2' => 'b'
];

$misalignedArrWithConcatenatedKeys = [$key . '_val1'  => 'a',
  $key . '_val2'  => 'b',
    $key . '_val3' => 'c',
      $key . '_val4' => 'd',
$key . '_val5' => 'e'];
