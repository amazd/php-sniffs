<?php
class Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_STRING ];

  } // register

  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    // Skip tokens that are the names of functions or classes
    // within their definitions. For example:
    // function myFunction...
    // "myFunction" is T_STRING but we should skip because it is not a
    // function or method *call*.
    $functionName    = $stackPtr;
    $ignoreTokens    = PHP_CodeSniffer_Tokens::$emptyTokens;
    $ignoreTokens[]  = T_BITWISE_AND;
    $functionKeyword = $phpcsFile->findPrevious( $ignoreTokens, ($stackPtr - 1), null, true );
    if ( $tokens[ $functionKeyword ]['code'] === T_FUNCTION || $tokens[ $functionKeyword ]['code'] === T_CLASS ) {
      return;
    }

    // If the next non-whitespace token after the function or method call
    // is not an opening parenthesis then it cant really be a *call*.
    $openBracket = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ($functionName + 1), null, true );
    if ( $tokens[ $openBracket ]['code'] !== T_OPEN_PARENTHESIS ) {
      return;
    }

    // No need to inspect functions with no arguments
    if ( $tokens[ ($openBracket + 1) ]['code'] === T_CLOSE_PARENTHESIS ) {
      return;
    }

    $closeBracket  = $tokens[ $openBracket ]['parenthesis_closer'];
    $nextSeparator = $openBracket;

    if ( $tokens[ ($closeBracket - 1) ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space before closing parenthesis';
      $phpcsFile->addError( $error, ($closeBracket), 'SpaceBeforeCloseParens' );
    } // if SpaceBeforeCloseParens

    if ( $tokens[ ($openBracket + 1) ]['code'] !== T_WHITESPACE ) {
      $error = 'Expected at least 1 space after opening parenthesis';
      $phpcsFile->addError( $error, ($openBracket), 'SpaceAfterOpenParens' );
    } // if SpaceAfterOpenParens


    while ( ($nextSeparator = $phpcsFile->findNext( [ T_COMMA, T_VARIABLE, T_CLOSURE ], ($nextSeparator + 1), $closeBracket )) !== false ) {

      if ( $tokens[ $nextSeparator ]['code'] === T_CLOSURE ) {
        $nextSeparator = $tokens[ $nextSeparator ]['scope_closer'];
        continue;
      }

      // Make sure the comma or variable belongs directly to this function call,
      // and is not inside a nested function call or array.
      $brackets    = $tokens[ $nextSeparator ]['nested_parenthesis'];
      $lastBracket = array_pop( $brackets );
      if ( $lastBracket !== $closeBracket ) {
        continue;
      }

      if ( $tokens[ $nextSeparator ]['code'] === T_COMMA ) {
        if ( $tokens[ ($nextSeparator - 1) ]['code'] === T_WHITESPACE ) {
          $error = 'Space found before comma in function call';
          $phpcsFile->addError( $error, $nextSeparator - 1, 'SpaceBeforeComma' );
        }

        if ( $tokens[ ($nextSeparator + 1) ]['code'] !== T_WHITESPACE ) {
          $error = 'No space found after comma in function call';
          $phpcsFile->addError( $error, $nextSeparator + 1, 'NoSpaceAfterComma' );
        }

      } // if next is comma

      else {

        // Token is a variable.
        $nextToken = $phpcsFile->findNext( PHP_CodeSniffer_Tokens::$emptyTokens, ($nextSeparator + 1), $closeBracket, true );
        if ( $nextToken !== false ) {

          if ( $tokens[ $nextToken ]['code'] === T_EQUAL ) {

            if ( ($tokens[ ($nextToken - 1) ]['code']) !== T_WHITESPACE ) {
              $error = 'Expected 1 space before = sign of default value';
              $phpcsFile->addError( $error, $stackPtr, 'NoSpaceBeforeEquals' );
            }

            if ( $tokens[ ($nextToken + 1) ]['code'] !== T_WHITESPACE ) {
              $error = 'Expected 1 space after = sign of default value';
              $phpcsFile->addError( $error, $stackPtr, 'NoSpaceAfterEquals' );
            }

          } // if next token is =

        } // if next token

      } // else

    } // while comma, var or closure

  } // process

} // Behance_Sniffs_Functions_FunctionCallArgumentSpacingSniff
