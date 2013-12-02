<?php
/**
 * Checks that class declarations are:
 *  - @todo
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Your Name <you@domain.net>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: $Id: coding-standard-tutorial.xml,v 1.9 2008-10-09 15:16:47 cweiske Exp $
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Your Name <you@domain.net>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Behance_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff {

  /**
   * The number of spaces code should be indented.
   *
   * @var int
   */
  public $indent = 2;

  /**
   * Returns an array of tokens this test wants to listen for.
   *
   * @return array
   */
  public function register()
  {

    return [
        T_CLASS,
        T_INTERFACE
    ];

  } // end register()


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

    $tokens    = $phpcsFile->getTokens();
    $errorData = [ $tokens[ $stackPtr ]['content'] ];

    if ( isset( $tokens[ $stackPtr ]['scope_opener'] ) === false ) {

        $error = 'Possible parse error: %s missing opening or closing brace';

        $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);

        return;

    } // if scope_opener not present

    $curlyBrace  = $tokens[ $stackPtr ]['scope_opener'];
    $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
    $classLine   = $tokens[ $lastContent ]['line'];
    $braceLine   = $tokens[ $curlyBrace ]['line'];

    if ($braceLine !== $classLine) {

      $error = 'Opening brace of a %s must be on the same line as the definition';

      $phpcsFile->addError($error, $curlyBrace, 'OpenBraceNewLine', $errorData);

      return;

    } // if braceline !== classline

    $beforeCurly = $curlyBrace - 1;

    if ( $tokens[ $beforeCurly ]['code'] === T_WHITESPACE ) {

      $prevContent = $tokens[ $beforeCurly ]['content'];

      if ( $prevContent === $phpcsFile->eolChar ) {
        $spaces = 0;
      }

      else {

        $blankSpace = substr( $prevContent, strpos( $prevContent, $phpcsFile->eolChar ) );
        $spaces     = strlen( $blankSpace );

      } // else

      if ( $spaces !== 1 ) {

        $error = 'Expected %s space before opening brace; %s found';
        $data  = [
            1,
            $spaces,
        ];

        $phpcsFile->addError($error, $curlyBrace, 'SpaceBeforeBrace', $data);

      } // if number of spaces not expected

    } // if whitespace before curly

    else {
      $phpcsFile->addError( 'No whitespace before opening curly brace', $curlyBrace, 'NoSpaceBeforeBrace' );
    }

  } // process

} // end class
