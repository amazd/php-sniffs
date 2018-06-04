<?php
class Behance_Sniffs_Keywords_KeywordParensSpacingSniff implements PHP_CodeSniffer_Sniff {

  /**
   * Returns an array of tokens this test wants to listen for.
   * Taken from http://www.php.net/manual/en/reserved.keywords.php
   *
   * @return array
   */
  public function register() {

    return [
      T_ARRAY,
      T_CATCH,
      T_ECHO,
      T_EMPTY,
      T_EVAL,
      T_EXIT,
      T_INCLUDE,
      T_INCLUDE_ONCE,
      T_ISSET,
      T_LIST,
      T_PRINT,
      T_REQUIRE,
      T_REQUIRE_ONCE,
      T_UNSET
    ];

  } // register

  /**
   * Processes this test, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
   * @param int                  $stackPtr  The position of the current token in the
   *                                        stack passed in $tokens.
   *
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();

    $nextNonEmpty = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);
    $hasNoParens = $tokens[$nextNonEmpty]['code'] !== T_OPEN_PARENTHESIS;

    if ($hasNoParens) {
      $code = $tokens[$stackPtr]['code'];
      if ($code === T_PRINT || $code === T_ECHO || $code === T_EXIT) {
        return;
      }

      $error = 'Expected parentheses for keyword ' . $tokens[$stackPtr]['content'];
      $phpcsFile->addError($error, $stackPtr + 1, 'MissingParens');
      return;

    } // if hasNoParens

    if ($tokens[$stackPtr + 1]['code'] !== T_OPEN_PARENTHESIS) {
      $error = 'Expected no space before opening parenthesis';
      $should_fix = $phpcsFile->addFixableError($error, $stackPtr + 1, 'NoSpaceBeforeOpenParens');
      if ($should_fix) {
        $phpcsFile->fixer->replaceToken($stackPtr + 1, '');
      }
      return;
    } // if not parens

    $openingSpace = $tokens[$stackPtr + 2]['code'];

    // No need to inspect calls with no arguments
    if ($openingSpace === T_CLOSE_PARENTHESIS) {
      return;
    }

    if ($openingSpace === T_WHITESPACE && $tokens[$stackPtr + 2]['content'] !== $phpcsFile->eolChar) {
      $error = 'Expected no space after opening parenthesis';
      $should_fix = $phpcsFile->addFixableError($error, $stackPtr + 2, 'NoSpaceAfterOpenParens');
      if ($should_fix) {
        $phpcsFile->fixer->replaceToken($stackPtr + 2, '');
      }
    } // if whitespace

    $closeParens = $phpcsFile->findNext(T_CLOSE_PARENTHESIS, $stackPtr + 1);
    $closeContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($closeParens - 1), null, true);

    if ($tokens[$closeContent]['line'] === $tokens[$closeParens]['line'] && $closeParens - $closeContent !== 1) {
      $error = 'Expected no space before close parenthesis';
      $should_fix = $phpcsFile->addFixableError($error, $closeParens, 'NoSpaceBeforeCloseParens');
      if ($should_fix) {
        $phpcsFile->fixer->replaceToken($closeParens - 1, '');
      }
      return;
    } // if space before close

  } // process

} // Behance_Sniffs_Keywords_KeywordParensSpacingSniff
