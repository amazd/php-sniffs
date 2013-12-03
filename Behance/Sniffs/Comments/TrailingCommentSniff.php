<?php
/**
 * Ensures that trailing comments conform to standards
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */

/**
 * Ensures that trailing comments conform to standards
 * Applied to anything with a closing curly bracket
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */
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

    // get the comment
    $commentPtr = $stackPtr + 2;

    if ( !isset( $tokens[ $commentPtr ] ) || $tokens[ $commentPtr ]['type'] !== 'T_COMMENT' ) {

      $phpcsFile->addError( 'Disallowed use of scope operator detected', $stackPtr );

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
