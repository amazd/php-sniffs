<?php
/**
 * Ensures that trailing comments conform to standards
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Your Name <you@domain.net>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Ensures that trailing comments are valid:
 *   - Function declarations
 *   - Construct declaractions
 *   - Control structures (when they're more than 1 line)
 * 
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Your Name <you@domain.net>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
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

      $numberOfNewlines = 0;
      $scopeEndPtr      = $stackPtr;
      $scopeBeginPtr    = $tokens[ $scopeEndPtr ]['scope_opener'];

      for ( $ptr = $scopeBeginPtr; $ptr != $scopeEndPtr; ++$ptr ) {

        if ( $tokens[ $ptr ]['content'] === PHP_EOL ) {
          ++$numberOfNewlines;
        }

      } // for scopeBegin to scopeEnd

      $numberOfLines = max( 0, $numberOfNewlines - 1 );

      if ( $numberOfLines >= $this->minLinesRequiredForTrailing ) {

        $error = 'Missing required trailing comment for scope greater than % lines; found %s lines';
        $data  = [ $this->minLinesRequiredForTrailing, $numberOfLines ];

        $phpcsFile->addError( $error, $stackPtr, 'MissingTrailingComment', $data );

      } // if # of lines > 1 in scope

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

      } // if strlen whitespace > 1

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

} // Behance_Sniffs_Comments_TrailingCommentSniff
