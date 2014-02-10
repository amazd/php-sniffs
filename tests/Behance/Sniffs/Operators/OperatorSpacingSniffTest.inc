<?php
$goodbye = "Goodbye,";
$world = "world!";

echo $goodbye.' cruel '.$world;
echo $goodbye . ' cruel ' . $world;
echo $goodbye  .  ' cruel '  .  $world;
echo $goodbye
               .  ' cruel '
                  .  $world;

echo 1+2;
echo 1 + 2;
echo 1  +  2;
echo 1
       +
         2;

echo 1-2;
echo 1 - 2;
echo 1  -  2;
echo 1
       -
         2;

echo 1%2;
echo 1 % 2;
echo 1  %  2;
echo 1
       %
         2;

echo 1/2;
echo 1 / 2;
echo 1  /  2;
echo 1
       /
         2;

echo 1*2;
echo 1 * 2;
echo 1  *  2;
echo 1
       *
         2;

echo 1^2;
echo 1 ^ 2;
echo 1  ^  2;
echo 1
       ^
         2;

echo 1&2;
echo 1 & 2;
echo 1  &  2;
echo 1
       &
         2;

echo 1|2;
echo 1 | 2;
echo 1  |  2;
echo 1
       |
         2;

echo 1 and 2;
echo 1  and  2;
echo 1
       and
         2;

echo 1 or 2;
echo 1  or  2;
echo 1
       or
         2;

echo 1 xor 2;
echo 1  xor  2;
echo 1
       xor
         2;

$goodbye.=$world;
$goodbye .= $world;
$goodbye  .=  $world;
$goodbye
          .=
              $world;

$goodbye+=$world;
$goodbye += $world;
$goodbye  +=  $world;
$goodbye
          +=
              $world;

$goodbye-=$world;
$goodbye -= $world;
$goodbye  -=  $world;
$goodbye
          -=
              $world;

$goodbye/=$world;
$goodbye /= $world;
$goodbye  /=  $world;
$goodbye
          /=
              $world;

$goodbye%=$world;
$goodbye %= $world;
$goodbye  %=  $world;
$goodbye
          %=
              $world;

$goodbye*=$world;
$goodbye *= $world;
$goodbye  *=  $world;
$goodbye
          *=
              $world;

$goodbye<>$world;
$goodbye <> $world;
$goodbye  <>  $world;
$goodbye
          <>
              $world;

$goodbye&=$world;
$goodbye &= $world;
$goodbye  &=  $world;
$goodbye
          &=
              $world;

$arr = [
    'blah'=>'blah',
    'blah' => 'blah',
    'blah'     =>     'blah',
    'blah'
               =>
                      'blah'
];

$truthy = 1==2;
$truthy = 1 == 2;
$truthy = 1  ==  2;
$truthy = 1
              ==
                   2;

$truthy = 1===2;
$truthy = 1 === 2;
$truthy = 1  ===  2;
$truthy = 1
              ===
                   2;

$truthy = 1!=2;
$truthy = 1 != 2;
$truthy = 1  !=  2;
$truthy = 1
              !=
                   2;

$truthy = 1!==2;
$truthy = 1 !== 2;
$truthy = 1  !==  2;
$truthy = 1
              !==
                   2;

$truthy = 1<2;
$truthy = 1 < 2;
$truthy = 1  <  2;
$truthy = 1
              <
                   2;

$truthy = 1>2;
$truthy = 1 > 2;
$truthy = 1  >  2;
$truthy = 1
              >
                   2;

$truthy = 1>=2;
$truthy = 1 >= 2;
$truthy = 1  >=  2;
$truthy = 1
              >=
                   2;

$truthy = 1<=2;
$truthy = 1 <= 2;
$truthy = 1  <=  2;
$truthy = 1
              <=
                   2;

$foo++;
$foo ++;
++ $foo;

$foo--;
$foo --;
--$foo;
-- $foo;

!$foo;
! $foo; // BAD

$foo =& $bar;
$foo = & $bar; // BAD
$foo = &$bar;  // BAD
$foo =&
        $bar;

// more cases for minus - unary too
$foo = - 5;           // no space for flat LNUMBER
$foo = -5;
$foo = $bar-$bleh;    // spaces required
$foo = $bar - $bleh;
$foo = -$bleh;        // parens required
$foo = - $bleh;       // parens required
$foo = -($bleh);
