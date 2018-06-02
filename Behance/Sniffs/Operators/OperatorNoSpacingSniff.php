<?php

class Behance_Sniffs_Operators_OperatorNoSpacingSniff extends Behance_AbstractSniff {

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [T_DOUBLE_COLON];

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $this->_ensureNoSpaceAround($phpcsFile, $stackPtr, 'this operator', 'Operator');

  } // process

} // Behance_Sniffs_Operators_OperatorNoSpacingSniff
