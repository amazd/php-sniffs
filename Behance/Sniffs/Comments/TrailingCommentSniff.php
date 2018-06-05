<?php
class Behance_Sniffs_Comments_TrailingCommentSniff extends Behance_AbstractSniff {

  public $minLinesRequiredForTrailing = 4;

  protected $descriptionNotRequired = [
    T_TRY,
    T_ELSE
  ];

  protected $nameTrailing = [
    T_FUNCTION => 'function',
    T_CLASS => 'class',
    T_INTERFACE => 'interface',
    T_TRAIT => 'trait'
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return PHP_CodeSniffer_Tokens::$scopeOpeners;

  }
  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   *
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();

    // ignore inline scopes
    if (!isset($tokens[$stackPtr]['scope_opener'])) {
      return;
    }

    $openCurly = $tokens[$stackPtr]['scope_opener'];
    $closeCurly = $tokens[$openCurly]['scope_closer'];
    $nextContent = $phpcsFile->findNext([T_WHITESPACE, T_SEMICOLON], $closeCurly + 1, null, true);

    if (
      // ignore non-curly scopes such as the 'case' and 'default' keywords
      $tokens[$openCurly]['content'] === '{' &&
      // ignore single line scopes
      $tokens[$closeCurly]['line'] !== $tokens[$stackPtr]['line'] &&
      isset($tokens[$nextContent]) &&
      $tokens[$nextContent]['code'] === T_COMMENT &&
      mb_substr($tokens[$nextContent]['content'], 0, 2) === '//' &&
      $tokens[$nextContent]['line'] === $tokens[$closeCurly]['line']
    ) {
      $error = 'Trailing Comment Not Allowed';
      $should_fix = $phpcsFile->addFixableError($error, $nextContent, 'TrailingComment');
      if ($should_fix) {
        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($nextContent, $phpcsFile->eolChar);
        $nextContent--;
        while ($nextContent > $closeCurly && $tokens[$nextContent]['code'] !== T_SEMICOLON) {
          $phpcsFile->fixer->replaceToken($nextContent, '');
          $nextContent--;
        }
        $phpcsFile->fixer->endChangeset();
      }
    }

  }
}
