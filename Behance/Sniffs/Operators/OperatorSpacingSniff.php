<?php
class Behance_Sniffs_Operators_OperatorSpacingSniff extends Behance_AbstractSniff {

  /**
   * @var array
   *
   * Unary operators that may or may not require spaces after them
   * depending on their context
   */
  protected $_unary = [
    T_BITWISE_AND,
    T_MINUS,
    T_BOOLEAN_NOT,
    T_INC,
    T_DEC
  ];

  /**
   * @var array
   *
   * Tokens before an operator that *can* be unary that would indicate
   * that it's actually being used in a unary context, will be defined in process()
   */
  protected $_unaryIndicators;


  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    $this->_unaryIndicators = array_merge(
      PHP_CodeSniffer_Tokens::$comparisonTokens,
      PHP_CodeSniffer_Tokens::$assignmentTokens,
      [
        T_COLON,
        T_COMMA,
        T_INLINE_ELSE,
        T_INLINE_THEN,
        T_OPEN_PARENTHESIS,
        T_OPEN_SQUARE_BRACKET,
        T_OPEN_TAG,
        T_RETURN
      ]
    );

    return array_unique(array_merge(
      PHP_CodeSniffer_Tokens::$booleanOperators,
      PHP_CodeSniffer_Tokens::$assignmentTokens,
      PHP_CodeSniffer_Tokens::$comparisonTokens,
      PHP_CodeSniffer_Tokens::$operators,
      $this->_unary,
      [
        T_STRING_CONCAT,
        T_INC,
        T_DEC,
        T_EQUAL,
      ]
    ));

  } // register

  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();
    // if token **can** be unary and successfully processed, return
    // otherwise, fallthrough to regular logic
    if (in_array($tokens[$stackPtr]['code'], $this->_unary)) {
      if ($this->_processUnary($phpcsFile, $stackPtr)) {
        return;
      }
    }

    $this->_ensureOneSpaceAround($phpcsFile, $stackPtr, 'operator', 'Operator', true);

  } // process

  /**
   * Process an operator that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the token was evaluated as a unary operator or not
   */
  protected function _processUnary(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();
    $code   = $tokens[$stackPtr]['code'];


    if ($code === T_BITWISE_AND) {
      return $this->_processAmpersand($phpcsFile, $stackPtr);
    }
    if ($code === T_MINUS) {
      return $this->_processMinus($phpcsFile, $stackPtr);
    }
    if ($code === T_INC || $code === T_DEC) {
      return $this->_processIncDec($phpcsFile, $stackPtr);
    }
    if ($code === T_BOOLEAN_NOT) {
      $this->_ensureNoSpaceAfter($phpcsFile, $stackPtr, 'boolean not', 'BooleanNot');
      return true;
    }

    return false;

  } // _processUnary

  /**
   * Process an ampersand that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the ampersand was evaluated as a unary operator or not
   */
  private function _processAmpersand(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens        = $phpcsFile->getTokens();
    $allowedTokens = [
      T_EQUAL,
      T_COMMA,
      T_DOUBLE_ARROW,
      T_OPEN_PARENTHESIS,
      T_AS
    ];
    $nonWhitespacePtr = $phpcsFile->findPrevious($allowedTokens, $stackPtr, null, false, null, true);

    if ($nonWhitespacePtr !== false && $tokens[$nonWhitespacePtr]['line'] === $tokens[$stackPtr]['line']) {
      $this->_ensureNoSpaceAfter($phpcsFile, $stackPtr, 'Unary &', 'UnaryAmp');
      return true;
    } // if nonWhitespace

    return false;

  } // _processAmpersand

  /**
   * Process an exclamation that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the exclamation was evaluated as a unary operator or not
   */
  private function _processIncDec(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens    = $phpcsFile->getTokens();
    $nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);

    // if ++ or -- is before a $, it's prefix, otherwise postfix
    if ($tokens[$nextToken]['code'] === T_VARIABLE) {
      $this->_ensureNoSpaceAfter($phpcsFile, $stackPtr, 'prefix inc/dec', 'IncDec');
    }
    else {
      $this->_ensureNoSpaceBefore($phpcsFile, $stackPtr, 'postfix inc/dec', 'IncDec');
    }

    return true;

  } // _processIncDec

  /**
   * Process a minus that is potentially being used in a unary context
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   * @return bool                           Whether the minus was evaluated as a unary operator or not
   */
  private function _processMinus(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens     = $phpcsFile->getTokens();
    $prevTokens = array_merge($this->_unaryIndicators, [
      T_CLOSE_PARENTHESIS,
      T_CLOSE_SQUARE_BRACKET,
      T_VARIABLE,
      T_LNUMBER,
      T_STRING
    ]);
    $before = $phpcsFile->findPrevious($prevTokens, $stackPtr - 1, null, false, null, true);

    // if any of these are immediately before the '-', then it should be in a unary context
    if (!in_array($tokens[$before]['code'], $this->_unaryIndicators)) {
      return false;
    }

    $this->_ensureNoSpaceAfter($phpcsFile, $stackPtr, 'unary minus', 'UnaryMinus');

    return true;

  } // _processMinus

} // Behance_Sniffs_Operators_OperatorSpacingSniff
