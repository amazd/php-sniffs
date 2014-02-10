<?php
class Behance_Sniffs_Operators_OperatorSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * @var array
   *
   * Unary operators that may or may not require spaces after them
   * depending on their context
   */
  protected $_unary = [
      T_EQUAL,
      T_BITWISE_AND,
      T_MINUS,
      T_BOOLEAN_NOT
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return array_unique( array_merge(
        PHP_CodeSniffer_Tokens::$assignmentTokens,
        PHP_CodeSniffer_Tokens::$comparisonTokens,
        PHP_CodeSniffer_Tokens::$equalityTokens,
        PHP_CodeSniffer_Tokens::$operators,
        $this->_unary,
        [ T_STRING_CONCAT ]
    ) );

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    // if token **can** be unary and successfully processed, return
    // otherwise, fallthrough to regular logic
    if ( in_array( $tokens[ $stackPtr ]['code'], $this->_unary ) ) {
      if ( $this->_processUnary( $phpcsFile, $stackPtr ) ) {
        return;
      }
    }

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $error = '"%s" operator requires whitespace before it';
      $data  = [ $tokens[ $stackPtr ]['content'] ];
      $phpcsFile->addError( $error, $stackPtr, 'OperatorPadding', $data );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE ) {
      $error = '"%s" operator requires whitespace after it';
      $data  = [ $tokens[ $stackPtr ]['content'] ];
      $phpcsFile->addError( $error, $stackPtr, 'OperatorPadding', $data );
    }

  } // process

  /**
   * Process an operator that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the token was evaluated as a unary operator or not
   */
  protected function _processUnary( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr ]['code'] === T_EQUAL && $tokens[ $stackPtr + 1 ]['code'] === T_BITWISE_AND ) {
      return true;
    }

    if ( $tokens[ $stackPtr ]['code'] === T_BITWISE_AND ) {
      return $this->_processAmpersand( $phpcsFile, $stackPtr );
    }
    if ( $tokens[ $stackPtr ]['code'] === T_MINUS ) {
      return $this->_processMinus( $phpcsFile, $stackPtr );
    }
    if ( $tokens[ $stackPtr ]['code'] === T_BOOLEAN_NOT ) {
      return $this->_processNot( $phpcsFile, $stackPtr );
    }

    return false;

  } // _processUnary

  /**
   * Process an ampersand that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the ampersand was evaluated as a unary operator or not
   */
  private function _processAmpersand( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens           = $phpcsFile->getTokens();
    $allowedTokens    = [
        T_EQUAL,
        T_COMMA
    ];
    $nonWhitespacePtr = $phpcsFile->findPrevious( $allowedTokens, $stackPtr - 1, null, false, null, true );

    if ( $tokens[ $nonWhitespacePtr ]['line'] !== $tokens[ $stackPtr ]['line'] ) {
      return false;
    }

    // Equal sign being used before ampersand - is unary (reference operator, not bitwise-and)

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_EQUAL && $tokens[ $nonWhitespacePtr ]['code'] !== T_COMMA ) {
      $error = "Ampersand is not immediately after '=' or after a comma.";
      $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
    }

    if ( $tokens[ $stackPtr ]['code'] === T_EQUAL && $tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE ) {
      $error = "Ampersand is not immediately followed by whitespace.";
      $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
    }

    if ( $tokens[ $stackPtr ]['code'] === T_COMMA ) {

      if ( $tokens[ $stackPtr + 1 ]['code'] !== T_VARIABLE ) {
        $error = "Ampersand is not immediately followed by a variable.";
        $phpcsFile->addError( $error, $stackPtr, 'AmpersandSpacing' );
      }

    } // if T_COMMA

    return true;

  } // _processAmpersand

  /**
   * Process an exclamation that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the exclamation was evaluated as a unary operator or not
   */
  private function _processNot( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $phpcsFile->addError( 'Boolean Not should have whitespace before it.', $stackPtr, 'BooleanNotSpacing' );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] === T_WHITESPACE ) {
      $phpcsFile->addError( 'Boolean Not should not have whitespace after it.', $stackPtr, 'BooleanNotSpacing' );
    }

    return true;

  } // _processNot

  /**
   * Process an exclamation that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the minus was evaluated as a unary operator or not
   */
  private function _processMinus( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens = $phpcsFile->getTokens();
    $before = $phpcsFile->findPrevious( [ T_VARIABLE, T_LNUMBER, T_EQUAL ], $stackPtr - 1, null, false, null, true );

    if ( $tokens[ $before ]['code'] !== T_EQUAL ) {
      return false;
    }

    if ( $tokens[ $stackPtr - 1 ]['code'] !== T_WHITESPACE ) {
      $phpcsFile->addError( "'-' requires whitespace before it.", $stackPtr, 'MinusSpacing' );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] === T_WHITESPACE ) {
      $phpcsFile->addError( "'-' as unary should not have whitespace after it.", $stackPtr, 'MinusSpacing' );
    }

    if ( $tokens[ $stackPtr + 1 ]['code'] === T_VARIABLE ) {
      $phpcsFile->addError( "'-' as unary on variable should have parenthesis around variable.", $stackPtr, 'MinusSpacing' );
    }

    return true;

  } // _processMinus

} // Behance_Sniffs_Operators_OperatorSpacingSniff
