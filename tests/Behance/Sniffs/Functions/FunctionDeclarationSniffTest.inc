<?php
// this comment should be ok?
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

  // magic methods, deemed ok
  public function __construct() {}
  public function __destruct() {}
  public function __call() {}
  public function __callstatic() {}
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

function badMultiLine(
  $arg1 = false ) {}

function newlineBeforeCurlyBad()
{
}

function tooManySpaces()     {}

function noWhitespaceBetweenArg($arg1 ) {}
function tooMuchWhitespace(      $arg1 ) {}
function noWhitespaceAfterArg( $arg1) {}
function tooMuchWhitespaceAfterArg( $arg1      ) {}

function singleLineFunctionNotEmpty() { $blah = 'foo' }
function noImmediateNewline() { $blah = 'foo'
}

// no newline right after curly is bad too
function noNewlineAboveCloser() {
  $blah = 'foo' }
