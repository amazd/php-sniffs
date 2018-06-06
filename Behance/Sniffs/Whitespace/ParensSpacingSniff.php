<?php
class Behance_Sniffs_Whitespace_ParensSpacingSniff extends Behance_AbstractSniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   * Taken from http://www.php.net/manual/en/reserved.keywords.php
   *
   * @return array
   */
  public function register() {

    return [
      T_OPEN_PARENTHESIS,
      T_CLOSE_PARENTHESIS
    ];

  }

  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();

    if ($tokens[$stackPtr]['code'] === T_OPEN_PARENTHESIS) {
      $this->_ensureNoSpaceAfter($phpcsFile, $stackPtr, 'open parenthesis', 'OpenParens', true);
    } else {
      $this->_ensureNoSpaceBefore($phpcsFile, $stackPtr, 'close parenthesis', 'CloseParens', true);
    }

  }

}
