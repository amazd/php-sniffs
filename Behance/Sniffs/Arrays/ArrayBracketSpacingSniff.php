<?php
class Behance_Sniffs_Arrays_ArrayBracketSpacingSniff implements PHP_CodeSniffer_Sniff {


  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [
        T_OPEN_SQUARE_BRACKET,
        T_CLOSE_SQUARE_BRACKET,
    ];

  } // register


  /**
   * Processes this sniff, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
   * @param int          $stackPtr  The position of the current token in the
   *                    stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[$stackPtr]['code'] === T_OPEN_SQUARE_BRACKET ) {
      $nonSpace   = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true );
      $direction  = 1;
      $position   = 'after opening';
      $code       = 'AfterOpen';
      $otherToken = T_CLOSE_SQUARE_BRACKET;
    } // if T_OPEN_SQUARE_BRACKET

    else {

      $nonSpace   = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true );
      $direction  = -1;
      $position   = 'before closing';
      $code       = 'BeforeClose';
      $otherToken = T_OPEN_SQUARE_BRACKET;

    } // else T_CLOSE_SQUARE_BRACKET

    // don't deal with multiline array dereferencing
    if ( $tokens[$nonSpace]['line'] !==  $tokens[$stackPtr]['line'] ) {
      return;
    }

    if ( $stackPtr + $direction !== $nonSpace ) {
      $should_fix = $phpcsFile->addFixableError( 'Expected 0 spaces %s bracket', $stackPtr, 'Spacing' . $code, [ $position ] );
      if ( $should_fix ) {
        $phpcsFile->fixer->replaceToken( $stackPtr + $direction, '' );
      }
    } // if stackPtr

  } // process

} // Behance_Sniffs_Arrays_ArrayBracketSpacingSniff
