<?php

class blah {
  protected function warningProtected() {}
  protected function _okProtected() {}

  private function badPrivate() {}

  private function _goodPrivate() {}

  // should be ok for phpunit
  protected function setUp() {}
  protected function tearDown() {}

  private function setUp() {}
  private function tearDown() {}

  // exceptions for workers ............... :(
  public function _start_work() {}
  public function _end_work() {}
  public function _flush_cache() {}

  // magic methods, deemed ok
  public function __construct() {}
  public function __destruct() {}
  public function __call() {}
  public function __callStatic() {} // must be camel case to check case insensitivity
  public function __get() {}
  public function __set() {}
  public function __isset() {}
  public function __unset() {}
  public function __sleep() {}
  public function __wakeup() {}
  public function __tostring() {}
  public function __set_state() {}
  public function __clone() {}
  public function __invoke() {}
  public function __call() {}

}

function test() {$a = 'hi
';}

function okmultiline(
  $arg1 = false) {}

function newlineBeforeCurlyBad()
{
}

function tooManySpaces()     {}

function argWhiteSpaceHandledElsewhere($arg1 ) {}
function argWhiteSpaceHandledElsewhere(      $arg1 ) {}
function argWhiteSpaceHandledElsewhere( $arg1) {}
function argWhiteSpaceHandledElsewhere( $arg1      ) {}

function singleLineFunctionNotEmpty() { $blah = 'foo'}
function noImmediateNewline() { $blah = 'foo'
}

// no newline right after curly is bad too
function noNewlineAboveCloser() {
  $blah = 'foo'}

interface AnInterface {
  public function aFunction();
}

function __notAllowed() {}

function needsAnotherNewLineAfterComment() {

  $a++;
  // this comment ruins it

} // needsAnotherNewLineAfterComment

function needsAnotherNewLineAfterComment() {

  $a++;/**/
} // needsAnotherNewLineAfterComment

function invalidReturnValue1() : string{}
function invalidReturnValue2(): string {}
function invalidReturnValue3() :string {}
function invalidReturnValue4() :    string {}
function invalidReturnValue4() : 7655445 {}

function usableReturnValue() : string {

  $a;

} // usableReturnValue

function anotherUsableReturnValue() : string {}

function usableReturnValue() : ?string {

  $a;

} // usableReturnValue

function anotherUsableReturnValue() : ?string {}

function notUsableReturnValue() : ? string {}

function anotherUsableReturnValue() : void {}

function anotherUsableReturnValue() : ? {}

function yetAnotherUsableReturnValue() : SimpleXMLElement {}

function yetAnotherUsableReturnValue() : \SimpleXMLElement {}

function yetAnotherUsableReturnValueOrNull() : ?\SimpleXMLElement {}

function thisWillFailBecauseSpaceBetweenNullableAndNameSpace() : ? \SimpleXMLElement {}

function yetAnotherUsableReturnValueWithNamespaces() : Behance\SimpleXMLElement {}

function yetAnotherUsableReturnValueWithNamespacesOrNull() : ?Behance\SimpleXMLElement {}
