<?php
/**
 * Ensures that array elements have 2 indents
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */

/**
 * Ensures that array elements have 2 indents
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */
class Behance_Sniffs_Arrays_ArrayIndentSniff implements PHP_CodeSniffer_Sniff {

  public $indent              = 2;
  public $elementIndentLevel  = 2;

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [
        T_ARRAY,
        T_OPEN_SHORT_ARRAY
    ];

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

    $tokens      = $phpcsFile->getTokens();

    // skip indentation checking for the array opener itself
    $ptr         = $stackPtr + 1;
    $closingPtr  = ( $tokens[ $stackPtr ]['type'] === 'T_ARRAY' )
                   ? $tokens[ $stackPtr ]['parenthesis_closer']
                   : $tokens[ $stackPtr ]['bracket_closer'];

    // same with the closing brace
    $closingPtr -= 1;

    for ( $ptr; $ptr < $closingPtr; ++$ptr ) {

      $token   = $tokens[ $ptr ];
      $content = $token['content'];

      if ( $token['type'] !== 'T_WHITESPACE' || $content === PHP_EOL ) {
        continue;
      }

      $spaces         = strlen( $content );
      $expectedSpaces = ( $token['level'] * $this->indent ) + ( $this->elementIndentLevel * $this->indent );

      if ( $spaces !== $expectedSpaces ) {

        $error = 'Array elements not properly indented; expected %s spaces, found %s';
        $code  = 'ArrayElementIndented' . $this->elementIndentLevel . 'Levels';
        $data  = [
            $expectedSpaces,
            $spaces
        ];

        $phpcsFile->addError( $error, $ptr, $code, $data );

      } // if spaces not expectedSpaces

    } // for ptr < closingPtr

  } // process

} // Behance_Sniffs_Arrays_ArrayIndentSniff
