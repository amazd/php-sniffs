<?php

class Behance_Sniffs_Functions_ChainedMethodAlignmentSniff implements PHP_CodeSniffer_Sniff {

  const TAB_WIDTH = 2;

  /** @var PHP_CodeSniffer_File $_phpcsFile */
  private $_phpcsFile;

  private $_tokens;
  private $_beginning_of_statement;

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_OBJECT_OPERATOR ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param  PHP_CodeSniffer_File  $phpcsFile The file being scanned.
   * @param  int                   $stackPtr  The position of the current token in the stack passed in $tokens.
   *
   * @return bool|void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $code                             = 'ChainedMethodAlignment';
    $this->_phpcsFile                 = $phpcsFile;
    $this->_tokens                    = $phpcsFile->getTokens();
    $this->_beginning_of_statement    = $this->_phpcsFile->findStartOfStatement( $stackPtr );
    $current_column                   = $this->_tokens[ $stackPtr ]['column'];
    $previous_object_operator_in_file = $this->_phpcsFile->findPrevious( T_OBJECT_OPERATOR, $stackPtr - 1 );

    if ( !$this->_getEndOfPreviousLine( $stackPtr ) ||
         !$previous_object_operator_in_file ||
         !$this->_lineBeginsWithObjectOperator( $stackPtr )
    ) {
      return;
    }

    $desired_column           = $this->_getDesiredColumn( $stackPtr );
    $correct_number_of_spaces = str_repeat( ' ', $desired_column - 1 );

    if ( $desired_column !== $current_column ) {

      $fix_option_enabled = $this->_phpcsFile->addFixableError( 'Please align method operator. Expected %s; found %s', $stackPtr, $code, [ $desired_column, $current_column ] );

      if ( $fix_option_enabled ) {
        $this->_fixAlignment( $correct_number_of_spaces, $stackPtr );
      }

    } // if an alignment error was found

  } // process

  /**
   * @param  int  $stackPtr
   *
   * @return bool
   */
  private function _lineBeginsWithObjectOperator( $stackPtr ) {

    $first_non_whitespace_character_on_line = $this->_phpcsFile->findFirstOnLine( T_WHITESPACE, $stackPtr, true );

    return $this->_tokens[ $first_non_whitespace_character_on_line ]['code'] === T_OBJECT_OPERATOR;

  } // _lineBeginsWithObjectOperator

  /**
   * @param  int  $stackPtr
   *
   * @return mixed
   */
  private function _getDesiredColumn( $stackPtr ) {

    return $this->_tokens[ $this->_phpcsFile->findStartOfStatement( $stackPtr ) ]['column'] + self::TAB_WIDTH;

  } // _getDesiredColumn

  /**
   * @param  int  $stackPtr
   *
   * @return bool|int
   */
  private function _getEndOfPreviousLine( $stackPtr ) {

    for ( $i = $this->_phpcsFile->findFirstOnLine( T_WHITESPACE, $stackPtr, true ); $i > $this->_beginning_of_statement; $i-- ) {
      if ( $this->_tokens[ $i ]['line'] !== $this->_tokens[ $stackPtr ]['line'] ) {
        return $i;
      }
    }

  } // _getEndOfPreviousLine

  /**
   * @param  int  $correct_number_of_spaces
   * @param  int  $stackPtr
   *
   * @return bool
   */
  private function _fixAlignment( $correct_number_of_spaces, $stackPtr ) {

    if ( $this->_tokens[ $stackPtr ]['column'] === 1 ) {
      return $this->_phpcsFile->fixer->addContentBefore( $stackPtr, $correct_number_of_spaces );
    }

    $this->_phpcsFile->fixer->replaceToken( ( $stackPtr - 1 ), $correct_number_of_spaces );

  } // _fixAlignment

} // Behance_Sniffs_Functions_ChainedMethodAlignmentSniff
