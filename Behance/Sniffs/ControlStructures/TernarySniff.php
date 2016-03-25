<?php
class Behance_Sniffs_ControlStructures_TernarySniff implements PHP_CodeSniffer_Sniff {

  private $_equals_sign_index;
  private $_phpcsFile;
  private $_stackPtr;
  private $_tokens;


  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register() {

    return [ T_INLINE_THEN, T_INLINE_ELSE ];

  } // register


  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param  PHP_CodeSniffer_File  $phpcsFile  The file being scanned.
   * @param  int                   $stackPtr   The position of the current token in the stack passed in $tokens.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $this->_phpcsFile         = $phpcsFile;
    $this->_tokens            = $phpcsFile->getTokens();
    $this->_stackPtr          = $stackPtr;
    $this->_equals_sign_index = $phpcsFile->findPrevious( T_EQUAL, ( $stackPtr ) );
    $current_column           = $this->_tokens[ $stackPtr ]['column'];
    $desired_column           = $this->_tokens[ $this->_equals_sign_index + 2 ]['column'];
    $current_token_code       = $this->_tokens[ $stackPtr ]['code'];
    $error                    = 'Please align ternary expression. Expected %s; found %s';
    $code                     = 'InlineTernary';
    $correct_number_of_spaces = str_repeat( ' ', $desired_column - 1 );

    if ( $this->_canBeIgnored( $current_token_code ) ) {
      return;
    }

    if ( $desired_column !== $current_column ) {

      $data = [ $desired_column, $current_column ];

      $fix_option_enabled = $this->_phpcsFile->addFixableError( $error, $this->_stackPtr, $code, $data );

      if ( $fix_option_enabled === true ) {
        $this->_fixAlignment( $correct_number_of_spaces, $current_column );
      }

    } // if an alignment error was found

  } // process


  /**
   * @return bool
   */
  private function _isSingleLineTernary() {

    $current_line = ( $this->_tokens[ $this->_stackPtr ]['line'] );
    $equals_line  = ( $this->_tokens[ $this->_equals_sign_index ]['line'] );

    return $current_line === $equals_line;

  } // _isSingleLineTernary


  /**
   * @param  int   $current_token_code
   *
   * @return bool
   */
  private function _isInnerTernary( $current_token_code ) {

    return ( $current_token_code === T_INLINE_ELSE && $this->_isInnerInlineElse() )
           || ( $current_token_code === T_INLINE_THEN && $this->_isInnerInlineThen() );

  } // _isInnerTernary


  /**
   * @param  int   $correct_number_of_spaces
   * @param  int   $current_column
   *
   * @return void
   */
  private function _fixAlignment( $correct_number_of_spaces, $current_column ) {

    if ( $current_column === 1 ) {
      $this->_phpcsFile->fixer->addContentBefore( $this->_stackPtr, $correct_number_of_spaces );
    }
    else {
      $this->_phpcsFile->fixer->replaceToken( ( $this->_stackPtr - 1 ), $correct_number_of_spaces );
    }

  } // _fixAlignment


  /**
   * @param  int   $current_token_code
   *
   * @return bool
   */
  private function _canBeIgnored( $current_token_code ) {

    return $this->_isSingleLineTernary() || ( $this->_isInnerTernary( $current_token_code ) );

  } // _canBeIgnored


  /**
   * @return bool
   */
  private function _isInnerInlineThen() {

    return $this->_phpcsFile->findPrevious( T_INLINE_THEN, $this->_stackPtr - 1, $this->_equals_sign_index );

  } // _isInnerInlineThen


  /**
   * @return bool
   */
  private function _isInnerInlineElse() {

    $closing_semicolon_index   = $this->_phpcsFile->findNext( T_SEMICOLON, $this->_stackPtr );
    $previous_inline_else      = $this->_phpcsFile->findPrevious( T_INLINE_ELSE, $this->_stackPtr - 1, $this->_equals_sign_index );
    $next_inline_else          = $this->_phpcsFile->findNext( T_INLINE_ELSE, $this->_stackPtr + 1, $closing_semicolon_index );
    $current_line              = $this->_tokens[ $this->_stackPtr ]['line'];
    $previous_inline_else_line = $this->_tokens[ $previous_inline_else ]['line'];
    $next_inline_else_line     = $this->_tokens[ $next_inline_else ]['line'];

    return ( ( $previous_inline_else && ( $current_line === $previous_inline_else_line ) )
           || ( $next_inline_else && ( $current_line !== $next_inline_else_line ) ) );

  } // _isInnerInlineElse


} // Behance_Sniffs_ControlStructures_TernarySniff
