<?php

class Behance_Sniffs_Operators_EqualsAlignmentSniff implements PHP_CodeSniffer_Sniff {

  /** @var PHP_CodeSniffer_File $_phpcsFile */
  private $_phpcsFile;
  private $_stackPtr;
  private $_tokens;
  private $_current_column;
  private $_current_line;
  private $_previous_equals_sign;

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_EQUAL ];

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

    $this->_phpcsFile            = $phpcsFile;
    $this->_tokens               = $phpcsFile->getTokens();
    $this->_stackPtr             = $stackPtr;
    $this->_current_column       = $this->_tokens[ $stackPtr ]['column'];
    $this->_current_line         = $this->_tokens[ $stackPtr ]['line'];
    $this->_previous_equals_sign = $this->_phpcsFile->findPrevious( T_EQUAL, $this->_stackPtr - 1 );
    $code                        = 'EqualsAlignment';
    $next_token_column           = $this->_tokens[ $stackPtr + 2 ]['column'];
    $desired_column_after_equals = $this->_tokens[ $stackPtr ]['column'] + 2;

    if ( $this->_shouldBeIgnored() ) {
      return;
    }

    if ( $this->_isMultipleAssignment() ) {
      $this->_phpcsFile->addError( 'Multiple assignments cannot be made on a single line', $stackPtr, 'Equals Alignment' );
      return;
    }

    if ( $this->_extraSpaceOnRightSideOfEquals( $next_token_column, $desired_column_after_equals ) ) {
      $fix_option_enabled = $this->_phpcsFile->addFixableError( 'Please align token after equals sign. Expected %s; found %s', $this->_stackPtr, $code, [ $desired_column_after_equals, $next_token_column ] );

      if ( $fix_option_enabled ) {
        $correct_number_of_spaces = str_repeat( ' ', 1 );
        $this->_fixPostAlignment( $correct_number_of_spaces, $this->_stackPtr );
      }
    } // if there is extra space to the right of the equals sign

    if ( !$this->_startOfStatementIsFirstTokenOnLine( $this->_stackPtr ) ) {
      return;
    }

    $desired_column = $this->_getDesiredColumn();

    if ( $this->_equalsAlignmentIsIncorrect( $desired_column ) ) {
      $fix_option_enabled       = $this->_phpcsFile->addFixableError( 'Please align equals sign. Expected %s; found %s', $this->_stackPtr, $code, [ $desired_column, $this->_current_column ] );
      $correct_number_of_spaces = str_repeat( ' ', $desired_column - $this->_tokens[ $stackPtr - 1 ]['column'] );

      if ( $fix_option_enabled ) {
        $this->_fixPreAlignment( $correct_number_of_spaces, $this->_stackPtr );
      }
    } // if an alignment error was found

  } // process

  /**
   * @param  int   $desired_column
   *
   * @return bool
   */
  private function _equalsAlignmentIsIncorrect( $desired_column ) {

    if ( $desired_column !== $this->_current_column ) {
      return true;
    }

  } // _equalsAlignmentIsIncorrect

  /**
   * @param  int   $next_token_column
   * @param  int   $desired_column_after_equals
   *
   * @return bool
   */
  private function _extraSpaceOnRightSideOfEquals( $next_token_column, $desired_column_after_equals ) {

    if ( $next_token_column !== $desired_column_after_equals ) {
      return true;
    }

  } // _extraSpaceOnRightSideOfEquals

  /**
   * @return bool
   */
  private function _isMultipleAssignment() {

    $next_equals = $this->_phpcsFile->findNext( T_EQUAL, $this->_stackPtr + 1 );

    if ( $this->_phpcsFile->findEndOfStatement( $this->_stackPtr ) > $next_equals
        && $this->_tokens[ $next_equals ]['line'] === $this->_current_line ) {
      return true;
    }

  } // _isMultipleAssignment

  /**
   * @return bool
   */
  private function _shouldBeIgnored() {

    if ( $this->_tokens[ $this->_previous_equals_sign ]['line'] === $this->_current_line

        || $this->_previous_equals_sign > $this->_phpcsFile->findStartOfStatement( $this->_stackPtr ) ) {

      return true;
    }

  } // _shouldBeIgnored

  /**
   * @param  int   $stack_pointer
   *
   * @return bool
   */
  private function _startOfStatementIsFirstTokenOnLine( $stack_pointer ) {

    $start_of_statement           = $this->_phpcsFile->findStartOfStatement( $stack_pointer );
    $first_non_whitespace_on_line = $this->_phpcsFile->findFirstOnLine( [ T_WHITESPACE ], $stack_pointer ) + 1;

    // or if the start of statement is at column 1

    return ( $start_of_statement === $first_non_whitespace_on_line ) || ( $this->_tokens[ $start_of_statement ]['column'] === 1 );

  } // _startOfStatementIsFirstTokenOnLine

  /**
   * @return int
   */
  private function _getDesiredColumn() {

    $stack_pointer  = $this->_stackPtr;
    $desired_column = $this->_tokens[ $stack_pointer - 1 ]['column'] + 1;

    $desired_column = ( $this->_previousMaximumDesiredColumn() > $desired_column )
                      ? $this->_previousMaximumDesiredColumn()
                      : $desired_column;

    $desired_column = ( $this->_followingMaximumDesiredColumn() > $desired_column )
                      ? $this->_followingMaximumDesiredColumn()
                      : $desired_column;

    return $desired_column;

  } // _getDesiredColumn

  /**
   * @return int
   */
  private function _followingMaximumDesiredColumn() {

    $stack_pointer  = $this->_stackPtr;
    $desired_column = $this->_tokens[ $stack_pointer - 1 ]['column'] + 1;

    while ( $stack_pointer < $this->_phpcsFile->numTokens - 0 ) {

      $next_equals         = $this->_phpcsFile->findNext( T_EQUAL, $stack_pointer + 1 );
      $current_line_number = $this->_tokens[ $stack_pointer ]['line'];

      if ( !$next_equals ) {
        break;
      }

      if ( !$this->_startOfStatementIsFirstTokenOnLine( $stack_pointer )
          || $this->_tokens[ $next_equals ]['line'] - $current_line_number > 1 ) {
        break;
      }

      // set the $desired_column to the column with the greater value
      if ( $this->_tokens[ $next_equals - 1 ]['column'] + 1 > $desired_column ) {
        $desired_column = $this->_tokens[ $next_equals - 1 ]['column'] + 1;
      }

      $stack_pointer = $next_equals;

    } // while there are tokens to check

    return $desired_column;

  } // _followingMaximumDesiredColumn

  /**
   * @return int
   */
  private function _previousMaximumDesiredColumn() {

    $stack_pointer  = $this->_stackPtr;
    $desired_column = $this->_tokens[ $stack_pointer - 1 ]['column'] + 1;

    // check preceding equals signs until a blank line is encountered
    while ( $stack_pointer > 0 ) {

      $previous_equals     = $this->_phpcsFile->findPrevious( T_EQUAL, $stack_pointer - 1 );
      $current_line_number = $this->_tokens[ $stack_pointer ]['line'];

      if ( !$previous_equals
          || !$this->_startOfStatementIsFirstTokenOnLine( $stack_pointer )
          || $current_line_number - $this->_tokens[ $previous_equals ]['line'] > 1) {
        break;
      }

      if ( $this->_tokens[ $previous_equals - 1 ]['column'] + 1 > $desired_column ) {
        $desired_column = $this->_tokens[ $previous_equals - 1 ]['column'] + 1;
      }

      $stack_pointer = $previous_equals;

    } // while index is greater than 0

    return $desired_column;

  } // _previousMaximumDesiredColumn

  /**
   * @param  int $correct_number_of_spaces
   * @param  int $current_index
   */
  private function _fixPreAlignment( $correct_number_of_spaces, $current_index ) {

    $this->_phpcsFile->fixer->replaceToken( ( $current_index - 1 ), $correct_number_of_spaces );

  } // _fixPreAlignment

  /**
   * @param  int   $correct_number_of_spaces
   * @param  int   $current_index
   */
  private function _fixPostAlignment( $correct_number_of_spaces, $current_index ) {

    $this->_phpcsFile->fixer->replaceToken( ( $current_index + 1 ), $correct_number_of_spaces );

  } // _fixPostAlignment

} // Behance_Sniffs_Operators_EqualsAlignmentSniff
