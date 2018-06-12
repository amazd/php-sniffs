<?php
class Behance_Sniffs_Functions_FunctionDeclarationSniff extends Behance_AbstractSniff {

  const INCORRECT_PREFIX = 'IncorrectFunctionPrefix';
  const INCORRECT_DOUBLE_UNDERSCORE = 'IncorrectDoubleUnderscoreFunctionPrefix';
  const INCORRECT_NEWLINES = 'InvalidFunctionNewlineFormatting';
  const INCORRECT_CURLY = 'InvalidFunctionCurlySpacing';
  const INVALID_ARG_FORMAT = 'InvalidArgumentListFormat';
  const MULTILINE_FUNC = 'MultilineFunctionsNotAllowed';
  const NON_EMPTY_SINGLELINE = 'NonEmptySingleLine';
  const INVALID_RETURN_VALUE = 'ReturnValueAfterColon';

  public $functionScopePrefixes = [
    'private' => '_',
    'protected' => '_',
    'public' => ''
  ];

  /**
   * A list of methods where a double underscore is allowed as a prefix
   *
   * @var array
   */
  public $doubleUnderscoreAllowedMethods = [
    'init'
  ];

  /**
   * A list of methods where a single underscore is allowed as a prefix
   *
   * @var array
   */
  public $prefixExemptions = [
    'protected' => [
      'setUp',    // phpunit
      'tearDown'  // phpunit
    ],
    'public' => [
      '_start_work', // workers
      '_end_work',   // workers
      '_flush_cache', // workers
    ],
    'private' => []
  ];

  /**
   * A list of all PHP magic methods. Must always be declared here in
   * all lower case.
   *
   * @var array
   */
  protected $magicMethods = [
    'construct',
    'destruct',
    'call',
    'callstatic',
    'get',
    'set',
    'isset',
    'unset',
    'sleep',
    'wakeup',
    'tostring',
    'set_state',
    'clone',
    'invoke',
    'call',
  ];

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [T_FUNCTION];

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

    $this->_processFunctionName($phpcsFile, $stackPtr);
    $this->_processDefinitionWhitespace($phpcsFile, $stackPtr);

  }

  /**
   * Makes sure that words in the function definition are spaced well
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processDefinitionWhitespace($phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();

    $indices = [
      'parenthesis_opener',
      'parenthesis_closer',
      'scope_opener'
    ];

    $missingIndices = array_diff($indices, array_keys($tokens[$stackPtr]));

    // interface functions don't have parens
    if (!empty($missingIndices)) {
      return;
    }

    $parenClose = $tokens[$stackPtr]['parenthesis_closer'];
    $openingBrace = $tokens[$stackPtr]['scope_opener'];
    $closingBrace = $tokens[$stackPtr]['scope_closer'];
    $returnColon = $phpcsFile->findNext(T_COLON, $parenClose, $openingBrace);
    $afterOpeningBrace = $phpcsFile->findNext(T_WHITESPACE, $openingBrace + 1, null, true);

    if ($afterOpeningBrace === $closingBrace) {
      $this->_ensureNoSpaceBefore($phpcsFile, $closingBrace, 'empty function close curly', 'EmptyCloseCurly');
      $this->_ensureNoSpaceAfter($phpcsFile, $openingBrace, 'empty function open curly', 'EmptyOpenCurly');
    } else {
      $this->_ensureNewLineBefore($phpcsFile, $closingBrace, 'close curly', 'CloseCurly', 2);
      $this->_ensureNewLineAfter($phpcsFile, $openingBrace, 'open curly', 'OpenCurly', 2);
    }
    $this->_ensureSameLine($phpcsFile, $parenClose, $openingBrace, 'close parens', 'open curly', self::INCORRECT_CURLY);

    if ($returnColon !== false) {
      $this->_ensureOneSpaceAround($phpcsFile, $returnColon, 'colon', 'Colon');
      $this->_ensureReturnValueAfterColon($phpcsFile, $returnColon, static::INVALID_RETURN_VALUE);
    } else {
      $this->_ensureOneSpaceBefore($phpcsFile, $openingBrace, 'open curly', 'OpenCurly');
    }

  }

  /**
   * Make sure that the function name is correctly formatted
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   */
  protected function _processFunctionName(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $methodProps = $phpcsFile->getMethodProperties($stackPtr);
    $scope = $methodProps['scope'];
    $fxName = $phpcsFile->getDeclarationName($stackPtr);
    $expectedPrefix = $this->functionScopePrefixes[$scope];

    $doubleUnderAllowed = array_merge($this->magicMethods, $this->doubleUnderscoreAllowedMethods);

    if (mb_strpos($fxName, '__') === 0) {
      if (in_array(mb_strtolower(mb_substr($fxName, 2)), $doubleUnderAllowed)) {
        return;
      } else {
        $error = '__ is a reserved prefix for magic functions';
        $phpcsFile->addError($error, $stackPtr, static::INCORRECT_DOUBLE_UNDERSCORE);
      }
    }

    // expected prefix is empty - just return, anything can happen
    if (empty($expectedPrefix)) {

      foreach ($this->functionScopePrefixes as $prefix) {
        if ($prefix && mb_strpos($fxName, $prefix) === 0 && (!in_array($fxName, $this->prefixExemptions[$scope]))) {
          $error = 'Expected no prefix for %s function "%s"; found "%s"';
          $phpcsFile->addError($error, $stackPtr, static::INCORRECT_PREFIX, [$scope, $fxName, $prefix]);
          return;
        }
      }

    } elseif (mb_strpos($fxName, $expectedPrefix) !== 0) {

      if (isset($this->prefixExemptions[$scope]) && in_array($fxName, $this->prefixExemptions[$scope])) {
        return;
      }

      $error = 'Expected prefix "%s" for %s function "%s" not found';
      $data = [$expectedPrefix, $scope, $fxName];

      if (mb_strtolower($scope) === 'protected') {
        return $phpcsFile->addWarning($error, $stackPtr, static::INCORRECT_PREFIX, $data);
      }

      $phpcsFile->addError($error, $stackPtr, static::INCORRECT_PREFIX, $data);

    }

  }

  /**
   * @param  PHP_CodeSniffer_File $phpcsFile
   * @param  int                  $stackPtr
   * @param  string               $tag
   */
  private function _ensureReturnValueAfterColon(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $tag) {

    $tokens = $phpcsFile->getTokens();

    $find = [
      T_NS_SEPARATOR,
      T_STRING,
      T_WHITESPACE,
    ];

    $nullable = false;
    $maybe_return_type = $phpcsFile->findNext($find, $stackPtr + 1, null, true, null, true);

    if ($tokens[$maybe_return_type]['code'] === T_NULLABLE) {
      $nullable = $maybe_return_type;
      $maybe_return_type = $phpcsFile->findNext($find, $maybe_return_type + 1, null, true, null, true);
    }

    if ($tokens[$maybe_return_type]['code'] !== T_RETURN_TYPE) {
      $error = 'Expected return value after colon';
      $phpcsFile->addError($error, $maybe_return_type, $tag);
      return;
    }

    if ($nullable) {
      $this->_ensureNoSpaceAfter($phpcsFile, $nullable, 'question mark', 'NullableReturnType');
    }

    $this->_ensureOneSpaceAfter($phpcsFile, $maybe_return_type, 'return type', 'ReturnType');

  }

}
