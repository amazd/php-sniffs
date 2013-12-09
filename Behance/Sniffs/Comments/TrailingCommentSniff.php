<?php
class Behance_Sniffs_Comments_TrailingCommentSniff implements PHP_CodeSniffer_Sniff {

  public $minLinesRequiredForTrailing = 4;

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_CLOSE_CURLY_BRACKET ];

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens       = $phpcsFile->getTokens();
    $nextTokenPtr = $stackPtr + 1;

    if ( !isset( $tokens[ $nextTokenPtr ] ) ) {
      return;
    }

    // comment exists right after curly brace
    if ( $tokens[ $nextTokenPtr ]['type'] == 'T_COMMENT' ) {
      $error = 'Single space required between closing curly brace & trailing comment';
      $phpcsFile->addError( $error, $stackPtr, 'MissingWhitespace' );
      return;
    }

    // newline right after closing brace - check that this is a control structure
    // and that it only has one line in it
    if ( $tokens[ $nextTokenPtr ]['content'] === PHP_EOL ) {

      $numberOfLines = $this->_numberOfLinesInScope( $tokens, $stackPtr );

      if ( $numberOfLines >= $this->minLinesRequiredForTrailing ) {
        $error = 'Missing required trailing comment for scope >= %s lines; found %s lines';
        $data  = [ $this->minLinesRequiredForTrailing, $numberOfLines ];
        $phpcsFile->addError( $error, $stackPtr, 'MissingTrailingComment', $data );
      }

      return;

    } // if there is no trailing comment

    // handle generic whitespace
    // at this point we're looking at a multiline scope
    if ( $tokens[ $nextTokenPtr ]['type'] == 'T_WHITESPACE' ) {

      $whitespacePtr = $nextTokenPtr;
      $amountOfSpace = 0;

      while ( isset( $tokens[ $whitespacePtr ] ) && $tokens[ $whitespacePtr ]['type'] === 'T_WHITESPACE' ) {

        $content        = str_replace( PHP_EOL, '', $tokens[ $whitespacePtr ]['content'] );
        $amountOfSpace += strlen( $content );

        ++$whitespacePtr;

      } // while isset && is whitespace

      if ( $amountOfSpace > 1 ) {
        $phpcsFile->addError( 'Too much whitespace detected after curly brace', $stackPtr );
        return;
      }

    } // if there is whitespace right after curly brace

    // make sure assignment closures have a semicolon
    if ( $this->_isCloseOfAnAssignedAnonymousFunction( $tokens, $stackPtr, $phpcsFile ) ) {
      if ( !isset( $tokens[ $stackPtr + 1 ] ) || $tokens[ $stackPtr + 1 ]['code'] !== T_SEMICOLON ) {
        $phpcsFile->addError( 'semicolon not found after anonymous function assignment', $stackPtr + 1 );

        return;
      } // if !semicolon

      $stackPtr++;
    } // if _isCloseOfAnAssignedAnonymousFunction

    // get the comment
    $commentPtr = $stackPtr + 2;

    if ( !isset( $tokens[ $commentPtr ] ) || $tokens[ $commentPtr ]['type'] !== 'T_COMMENT' ) {

      $phpcsFile->addError( 'trailing comment not found after closing curly', $stackPtr );

      return;

    } // no comment at all...?

    // final case: make sure that there is a single whitespace after the slashes
    $comment = ltrim( $tokens[ $commentPtr ]['content'], '/' );

    if ( strlen( $comment ) === 0 || $comment[0] !== ' ' ) {

      $phpcsFile->addError( 'Trailing comment formatted incorrectly; // <comment>', $stackPtr );

      return;

    } // if empty comment or missing whitespace

  } // process

  /**
   * closures need a semicolon before the trailing comment, but only when it's an assignment
   * so look for this situation:
   * 1. find the opening curly, 2. see if it's an anonymous function, 3. see if it's being assigned!
   *
   * @param   array $tokens
   * @param   int   $stackPtr
   * @param   PHP_CodeSniffer_File $phpcsFile
   * @return  boolean
   */
  protected function _isCloseOfAnAssignedAnonymousFunction ( $tokens, $stackPtr, PHP_CodeSniffer_File $phpcsFile ) {

    // 1. find the opening curly
    $curly_depth = 1;
    $prevToken = $stackPtr;
    while ( ( $prevToken = $phpcsFile->findPrevious( [ T_CLOSE_CURLY_BRACKET, T_OPEN_CURLY_BRACKET ], $prevToken - 1 ) ) !== false ) {
      if ( $tokens[$prevToken]['code'] === T_OPEN_CURLY_BRACKET ) {
        $curly_depth--;
      }
      else {
        $curly_depth++;
      }

      if ( $curly_depth === 0 ) {
        break;
      }
    } // while prevToken

    if ( $prevToken === false ) {
      return false;
    }

    $curlyOpenerPtr = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$scopeOpeners, $prevToken - 1 );

    if ( $curlyOpenerPtr === false || $tokens[$curlyOpenerPtr]['type'] !== 'T_CLOSURE' ) {
      return false;
    }


    $assignmentPtr = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$emptyTokens, $curlyOpenerPtr - 1, null, true );
    if ( $assignmentPtr === false ) {
      return false;
    }

    return in_array( $tokens[$assignmentPtr]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens );

  } // _isCloseOfAnAssignedAnonymousFunction

  /**
   * Starts from the *end* of the scope (ie: where '}' is)
   *
   * @param   array $tokens
   * @param   int   $stackPtr
   * @return  int
   */
  protected function _numberOfLinesInScope( $tokens, $stackPtr ) {

    $numberOfNewlines = 0;
    $scopeEndPtr      = $stackPtr;
    $scopeBeginPtr    = $tokens[ $scopeEndPtr ]['scope_opener'];

    for ( $ptr = $scopeBeginPtr; $ptr != $scopeEndPtr; ++$ptr ) {

      if ( $tokens[ $ptr ]['content'] === PHP_EOL ) {
        ++$numberOfNewlines;
      }

    } // for scopeBegin to scopeEnd

    return max( 0, $numberOfNewlines - 1 );

  } // _numberOfLinesInScope

} // Behance_Sniffs_Comments_TrailingCommentSniff
