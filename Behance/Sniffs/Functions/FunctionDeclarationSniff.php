<?php
/**
 * Makes sure that function / method definitions have:
 *
 * - curly brace on the same line as the definition
 * - an empty line right after the definition
 * - an empty line right before the closing brace
 * - the trailing comment is the name of the function
 *   - Formatting of the function name is sniffed via Comment.TrailingComment
 *
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */

/**
 * Makes sure that function / method definitions have:
 *
 * - curly brace on the same line as the definition
 * - an empty line right after the definition
 * - an empty line right before the closing brace
 * - the trailing comment is the name of the function
 *   - Formatting of the function name is sniffed via Comment.TrailingComment
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */
class Behance_Sniffs_Functions_FunctionDeclarationSniff implements PHP_CodeSniffer_Sniff {

  const INCORRECT_PREFIX   = 'IncorrectFunctionPrefix';
  const INCORRECT_NEWLINES = 'InvalidFunctionNewlineFormatting';
  const INVALID_TRAILING   = 'InvalidFunctionTrailingComment';
  const INVALID_ARG_FORMAT = 'InvalidArgumentListFormat';
  const MULTILINE_FUNC     = 'MultilineFunctionsNotAllowed';

  public $prefixLimit = 1;

  public $functionScopePrefixes = [
      T_PRIVATE    => '_',
      T_PROTECTED  => '_',
      T_PUBLIC     => ''
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_FUNCTION ];

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

    $this->_processFunctionName( $phpcsFile, $stackPtr );
    $this->_processDefinitionWhitespace( $phpcsFile, $stackPtr );
    $this->_processCurlyBraceNewlines( $phpcsFile, $stackPtr );
    $this->_processTrailingFunctionComment( $phpcsFile, $stackPtr );

  } // process

  /**
   * Makes sure that words in the function definition are spaced well
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processDefinitionWhitespace( $phpcsFile, $stackPtr ) {

    $tokens     = $phpcsFile->getTokens();
    $parenOpen  = $tokens[ $stackPtr ]['parenthesis_opener'];
    $parenClose = $tokens[ $stackPtr ]['parenthesis_closer'];

    if ( $tokens[ $parenOpen ]['line'] !== $tokens[ $parenClose ]['line'] ) {
      $error = 'Multiline function definitions not allowed';
      $phpcsFile->addError( $error, $stackPtr, static::MULTILINE_FUNC );
      return;
    }

    // valid - function blah()
    if ( $parenOpen + 1 === $parenClose ) {
      return;
    }

    // check whitespace after first parenth
    if ( $tokens[ $parenOpen + 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'No whitespace found between opening parenthesis & first argument';
      $phpcsFile->addError( $error, $stackPtr, static::INVALID_ARG_FORMAT );
    }
    elseif ( strlen( $tokens[ $parenOpen + 1 ]['content'] ) > 1 ) {
      $error = 'Expected 1 space between opening parenthesis & first argument; found %s';
      $data  = [ strlen( $tokens[ $parenOpen + 1 ]['content'] ) ];
      $phpcsFile->addError( $error, $stackPtr, static::INVALID_ARG_FORMAT, $data );
    }

    // whitespace after closing parenth
    if ( $tokens[ $parenClose - 1 ]['code'] !== T_WHITESPACE ) {
      $error = 'No whitespace found between last argument & closing parenthesis';
      $phpcsFile->addError( $error, $stackPtr, static::INVALID_ARG_FORMAT );
    }
    elseif ( strlen( $tokens[ $parenClose - 1 ]['content'] ) > 1 ) {
      $error = 'Expected 1 space between last argument & closing parenthesis; found %s';
      $data  = [ strlen( $tokens[ $parenClose - 1 ]['content'] ) ];
      $phpcsFile->addError( $error, $stackPtr, static::INVALID_ARG_FORMAT, $data );
    }

  } // _processDefinitionWhitespace

  /**
   * Trailing comment for functions MUST be the function name
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processTrailingFunctionComment( $phpcsFile, $stackPtr ) {

    $tokens     = $phpcsFile->getTokens();
    $fxName     = $tokens[ $stackPtr + 2 ]['content'];
    $closingPtr = $tokens[ $stackPtr ]['scope_closer'];

    $next = $phpcsFile->findNext( T_WHITESPACE, $closingPtr + 1, null, true );

    if ( $tokens[ $next ]['line'] !== $tokens[ $closingPtr ]['line'] ) {
      $error = 'Missing function trailing comment';
      $phpcsFile->addError( $error, $closingPtr, static::INVALID_TRAILING );
      return;
    }

    if ( $tokens[ $next ]['code'] !== T_COMMENT ) {
      $error = 'Unexpected token found after closing curly brace';
      $phpcsFile->addError( $error, $closingPtr, static::INVALID_TRAILING );
      return;
    }

    $expectedComment = "// {$fxName}";
    $actualComment   = trim( $tokens[ $next ]['content'] );

    if ( $expectedComment !== $actualComment ) {
      $error = 'Trailing comment for function "%s" incorrect; expected "%s", found "%s"';
      $data  = [ $fxName, $expectedComment, $actualComment ];
      $phpcsFile->addError( $error, $closingPtr, static::INVALID_TRAILING, $data );
    }

  } // _processTrailingFunctionComment

  /**
   * Makes sure that there is an empty line below the fxs opening curly brace
   * and one above the closing curly brace
   *
   * A tiny bit janky because PHP_EOLs in comments are not treated as separate
   * whitespace tokens
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processCurlyBraceNewlines( $phpcsFile, $stackPtr ) {

    $tokens       = $phpcsFile->getTokens();
    $openingBrace = $phpcsFile->findNext( T_OPEN_CURLY_BRACKET, $stackPtr );

    if ( $tokens[ $openingBrace + 1 ]['code'] === T_CLOSE_CURLY_BRACKET ) {
      return;
    }

    if ( $tokens[ $openingBrace + 1 ]['content'] !== PHP_EOL ) {
      $error = 'Newline not found immediately after opening curly bracket';
      $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_NEWLINES );
    }

    if ( $tokens[ $openingBrace + 2 ]['content'] !== PHP_EOL ) {
      $error = 'Empty line not found immediately function definition; there was trailing whitespace or non-whitespace';
      $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_NEWLINES );
    }

    $closingBrace = $tokens[ $openingBrace ]['bracket_closer'];
    $tracePtr     = $closingBrace - 1;
    $token        = $tokens[ $tracePtr ];

    while ( $token['content'] !== PHP_EOL && $token['code'] !== T_COMMENT ) {

      if ( $token['code'] !== T_WHITESPACE ) {
        $error = 'Non-whitespace found before closing curly brace';
        $phpcsFile->addError( $error, $tracePtr, static::INCORRECT_NEWLINES );
      }

      $token = $tokens[ --$tracePtr ];

    } // while content !== PHP_EOL

    $upperLineEnd   = $tokens[ $tracePtr - 1 ]['content'];
    $upperLineBegin = $tokens[ $tracePtr - 2 ]['content'];

    // should see two PHP_EOLs consecutively
    // as in:
    //   ...PHP_EOL
    //   PHP_EOL
    //   ...}
    if ( $upperLineEnd !== PHP_EOL && $upperLineBegin !== PHP_EOL ) {

      $hasCommentAbove = $tokens[ $tracePtr - 1 ]['code'] === T_COMMENT;

      // special case where a comment is directly above the empty newline
      // PHP_EOL is NOT treated as a separate token at this point
      if ( $hasCommentAbove ) {

        $comment = strrev( $tokens[ $tracePtr - 1 ]['content'] );

        if ( $comment[0] === PHP_EOL ) {
          return;
        }

      } // if has comment above curly brace line

      $error = 'No empty newline found above closing curly brace';
      $phpcsFile->addError( $error, $closingBrace, static::INCORRECT_NEWLINES );

    } // if not 2x PHP_EOL

  } // _processCurlyBraceNewlines

  /**
   * Make sure that the function name is correctly formatted
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processFunctionName( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $tokens         = $phpcsFile->getTokens();
    $functionScope  = $phpcsFile->findPrevious( PHP_CodeSniffer_Tokens::$scopeModifiers, $stackPtr );
    $functionScope  = $tokens[ $functionScope ];
    $expectedPrefix = $this->functionScopePrefixes[ $functionScope['code'] ];

    // expected prefix is empty - just return, anything can happen
    if ( empty( $expectedPrefix ) ) {
      return;
    }

    $fxName = $phpcsFile->findNext( T_STRING, $stackPtr );
    $fxName = $tokens[ $fxName ]['content'];

    if ( strpos( $fxName, $expectedPrefix ) !== 0 ) {

      $error = 'Expected prefix "%s" for %s function "%s" not found';
      $data  = [ $expectedPrefix, $functionScope['content'], $fxName ];

      $phpcsFile->addError( $error, $stackPtr, static::INCORRECT_PREFIX, $data );

    } // if expected prefix not at beginning

  } // _processFunctionName

} // Behance_Sniffs_Functions_FunctionDeclarationSniff
