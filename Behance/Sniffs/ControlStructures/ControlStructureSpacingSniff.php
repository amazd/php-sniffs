<?php
class Behance_Sniffs_ControlStructures_ControlStructureSpacingSniff implements PHP_CodeSniffer_Sniff {


  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

      return [
          T_IF,
          T_WHILE,
          T_FOREACH,
          T_FOR,
          T_SWITCH,
          T_DO,
          T_ELSE,
          T_ELSEIF,
      ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token
   *                                        in the stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( isset($tokens[$stackPtr]['parenthesis_opener']) === true ) {
      $parenOpener = $tokens[$stackPtr]['parenthesis_opener'];
      $parenCloser = $tokens[$stackPtr]['parenthesis_closer'];

      if ( $tokens[($parenOpener + 1)]['code'] !== T_WHITESPACE ) {
        $gap   = strlen( $tokens[($parenOpener + 1)]['content'] );
        $error = 'Expected at least 1 space after opening bracket';
        $data  = array($gap);
        $phpcsFile->addError( $error, ($parenOpener + 1), 'SpacingAfterOpenBrace', $data );
      } // if SpacingAfterOpenBrace

      if ( $tokens[($parenOpener - 1)]['code'] !== T_WHITESPACE ) {
        $gap   = strlen( $tokens[($parenOpener + 1)]['content'] );
        $error = 'Expected at least 1 space before opening bracket';
        $data  = array($gap);
        $phpcsFile->addError( $error, ($parenOpener + 1), 'SpacingBeforeOpenBrace', $data );
      } // if SpacingBeforeOpenBrace

      if ( $tokens[$parenOpener]['line'] === $tokens[$parenCloser]['line'] ) {

        if ( $tokens[($parenCloser - 1)]['code'] !== T_WHITESPACE ) {
          $gap   = strlen( $tokens[($parenCloser - 1)]['content'] );
          $error = 'Expected at least 1 space before closing bracket';
          $data  = array($gap);
          $phpcsFile->addError( $error, ($parenCloser - 1), 'SpaceBeforeCloseBrace', $data );
        } // if SpaceBeforeCloseBrace

        if ( $tokens[($parenCloser + 1)]['code'] !== T_WHITESPACE ) {
          $gap   = strlen( $tokens[($parenCloser + 1)]['content'] );
          $error = 'Expected at least 1 space before closing bracket';
          $data  = array($gap);
          $phpcsFile->addError( $error, ($parenCloser - 1), 'SpaceAfterCloseBrace', $data );
        } // if SpaceAfterCloseBrace

      } // if parens ends on same line as open

    } // end if

  } // process

} // end class
