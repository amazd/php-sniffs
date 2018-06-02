<?php
class Behance_Sniffs_Arrays_ArrayDeclarationSniff extends Behance_AbstractSniff {

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
  public function register() {

    return [T_OPEN_SHORT_ARRAY];

  } // register


  /**
   * Processes this sniff, when one of its tokens is encountered.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
   * @param int          $stackPtr  The position of the current token in the
   *                    stack passed in $tokens.
   *
   * @return void
   */
  public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {

    $tokens = $phpcsFile->getTokens();

    $arrayStart = $tokens[$stackPtr]['bracket_opener'];
    $arrayEnd   = $tokens[$arrayStart]['bracket_closer'];

    $indentPtr    = $phpcsFile->findFirstOnLine(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr, true);
    $indentStart  = $tokens[$indentPtr]['column'];
    $indentSpaces = $this->indent;
    $content      = $phpcsFile->findNext(T_WHITESPACE, ($arrayStart + 1), ($arrayEnd + 1), true);
    $lastContent  = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), $arrayStart, true);

    if ($content === $arrayEnd) {
      $this->_ensureNoSpaceAfter($phpcsFile, $arrayStart, 'array open', 'ArrayOpen');
      return;
    }

    if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
      $this->_handleSingleLineArray($phpcsFile, $arrayStart, $arrayEnd);
      return;
    } // if arrayStart line === arrayEnd line

    // Check the closing bracket is on a new line.
    if ($tokens[$lastContent]['line'] == ($tokens[$arrayEnd]['line'])) {
      $error      = 'Closer of array declaration must be on a new line';
      $should_fix = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNewLine');
      if ($should_fix) {
        $phpcsFile->fixer->addContentBefore($arrayEnd, $phpcsFile->eolChar);
      }
    } // if closing brace newline
    else {
      $this->_ensureAlignment($phpcsFile, $arrayEnd, $indentStart - 1, 'CloseBraceNotAligned', 'Closer of array');
    } // elseif arrayEnd column !== indentStart

    $nextToken  = $stackPtr;
    $lastComma  = $stackPtr;
    $keyUsed    = false;
    $singleUsed = false;
    $lastToken  = '';
    $indices    = [];

    // Find all the double arrows that reside in this scope.
    for ($nextToken = ($stackPtr + 1); $nextToken < $arrayEnd + 1; $nextToken++) {
      $currentEntry = [];

      if ($tokens[$nextToken]['code'] === T_OPEN_SHORT_ARRAY) {
        // Let subsequent calls of this test handle nested arrays.
        $nextTokenString = ( $tokens[$nextToken]['code'] === T_ARRAY )
                           ? 'parenthesis'
                           : 'bracket';

        $nextToken = $tokens[$tokens[$nextToken][$nextTokenString . '_opener']][$nextTokenString . '_closer'];
        continue;
      } // if T_OPEN_SHORT_ARRAY

      if ($tokens[$nextToken]['code'] === T_CLOSE_SHORT_ARRAY) {
        if ($keyUsed === false) {
          $valueContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($nextToken - 1), null, true);

          // Find the value, which will be the first token on the line,
          // excluding the leading whitespace.
          while ($tokens[$valueContent]['line'] === $tokens[$nextToken]['line']) {
            if ($valueContent === $arrayStart) {
              // Value must have been on the same line as the array
              // parenthesis, so we have reached the start of the value.
              break;
            }

            $valueContent--;
          } // while valueContent === nextToken

          $valueContent = $phpcsFile->findNext(T_WHITESPACE, ($valueContent + 1), $nextToken, true);
          $indices[]    = ['value' => $valueContent];
          $singleUsed   = true;
        } // if !keyUsed

        $lastToken = T_CLOSE_SHORT_ARRAY;
        continue;
      } // if code T_CLOSE_SHORT_ARRAY

      if ($tokens[$nextToken]['code'] === T_COMMA) {
        $lastComma = $nextToken;
        if ($this->_isNestedComma($tokens, $stackPtr, $nextToken)) {
          continue;
        }

        if ($keyUsed === true && $lastToken === T_COMMA) {
          $error = 'No key specified for array entry; first entry specifies key';
          $phpcsFile->addError($error, $nextToken, 'NoKeySpecified');
          return;
        }

        if ($keyUsed === false) {
          $valueContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($nextToken - 1), null, true);
          if ($tokens[$valueContent]['code'] !== T_COMMA) {
            $this->_ensureNoSpaceBefore($phpcsFile, $nextToken, 'comma', 'Comma');
          } // if nextToken === T_WHITESPACE

          // Find the value, which will be the first token on the line,
          // excluding the leading whitespace.
          while ($tokens[$valueContent]['line'] === $tokens[$nextToken]['line']) {
            if ($valueContent === $arrayStart) {
              // Value must have been on the same line as the array
              // parenthesis, so we have reached the start of the value.
              break;
            }

            $valueContent--;
          } // while valueContent === nextToken

          $valueContent = $phpcsFile->findNext(T_WHITESPACE, ($valueContent + 1), $nextToken, true);
          $indices[]    = ['value' => $valueContent];
          $singleUsed   = true;
        } // if !keyUsed

        $lastToken = T_COMMA;
        continue;
      } // if code T_COMMA

      if ($tokens[$nextToken]['code'] === T_DOUBLE_ARROW) {
        if ($singleUsed === true) {
          $error = 'Key specified for array entry; first entry has no key';
          $phpcsFile->addError($error, $nextToken, 'KeySpecified');
          return;
        }
        $keyUsed = true;

        $this->_ensureOneSpaceAround($phpcsFile, $nextToken, 'double arrow', 'Arrow');

        $currentEntry['index'] = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $lastComma + 1, $arrayEnd, true);
        $currentEntry['value'] = $phpcsFile->findNext([T_WHITESPACE], ($nextToken + 1), $arrayEnd, true);
        $indices[]             = $currentEntry;
        $lastToken             = T_DOUBLE_ARROW;
      } // if code = T_DOUBLE_ARROW
    } // for nextToken

    // Check for mutli-line arrays that should be single-line.
    $singleValue = false;

    if (empty($indices)) {
      $singleValue = true;
    }
    elseif (count($indices) === 1 && $lastToken === T_COMMA) {
      // There may be another array value without a comma.
      $exclude     = PHP_CodeSniffer_Tokens::$emptyTokens;
      $exclude[]   = T_COMMA;
      $nextContent = $phpcsFile->findNext($exclude, ($indices[0]['value'] + 1), $arrayEnd, true);
      if ($nextContent === false) {
        $singleValue = true;
      }
    } // elseif indices 1 and lastToken T_COMMA

    $indicesStart = ($indentStart + $indentSpaces);

    if ($keyUsed === false && !empty($indices)) {
      foreach ($indices as $value) {
        if (!empty($value['value']) && $tokens[$value['value'] - 1]['code'] === T_WHITESPACE) {
          $this->_ensureAlignment($phpcsFile, $value['value'], $indicesStart - 1, 'ValueNotAligned', 'Array value');
        }
      } // foreach indices
    } // if !keyUsed and !empty indices

    $numValues = count($indices);

    $indicesStart = ($indentStart + $indentSpaces);

    foreach ($indices as $index) {
      if (!isset($index['index'])) {
        // Array value only.
        if (($tokens[$index['value']]['line'] === $tokens[$stackPtr]['line']) && ($numValues > 1)) {
          $error      = 'The first value in a multi-value array must be on a new line';
          $should_fix = $phpcsFile->addFixableError($error, $index['value'], 'FirstValueNoNewline');
          if ($should_fix) {
            $phpcsFile->fixer->addContentBefore($index['value'], $phpcsFile->eolChar);
          }
        } // if value is on line with bracket

        continue;
      } // if index[index]

      if ($tokens[$index['index']]['line'] === $tokens[$stackPtr]['line']) {
        $error      = 'The first index in a multi-value array must be on a new line';
        $should_fix = $phpcsFile->addFixableError($error, $index['index'], 'FirstIndexNoNewline');
        if ($should_fix) {
          $phpcsFile->fixer->addContentBefore($index['index'], $phpcsFile->eolChar);
        }
        continue;
      } // if index is on line with bracket

      if ($this->_ensureAlignment($phpcsFile, $index['index'], $indicesStart - 1, 'KeyNotAligned', 'Array key')) {
        continue;
      }

      // Check each line ends in a comma.
      if ($tokens[$index['value']]['code'] !== T_OPEN_SHORT_ARRAY) {
        $valueLine = $tokens[$index['value']]['line'];
        $nextComma = false;
        for ($i = ($index['value'] + 1); $i < $arrayEnd; $i++) {
          // Skip bracketed statements, like function calls.
          if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
            $i         = $tokens[$i]['parenthesis_closer'];
            $valueLine = $tokens[$i]['line'];
            continue;
          }

          if ($tokens[$i]['code'] === T_COMMA) {
            $nextComma = $i;
            break;
          }
        } // for value -> arrayEnd

        // Check that there is no space before the comma.
        if ($nextComma !== false) {
          $this->_ensureNoSpaceBefore($phpcsFile, $nextComma, 'comma', 'Comma');
        } // if nextComma !false
      } // if !isArrayOpener
    } // foreach indices

  } // process

  protected function _handleSingleLineArray($phpcsFile, $arrayStart, $arrayEnd) {

    $tokens = $phpcsFile->getTokens();

    $this->_ensureNoSpaceAfter($phpcsFile, $arrayStart, 'array open', 'ArrayOpen');
    $this->_ensureNoSpaceBefore($phpcsFile, $arrayEnd, 'array close', 'ArrayClose');

    // Single line array.
    // Check if there are multiple values. If so, then it has to be multiple lines
    // unless it is contained inside a function call or condition.
    $valueCount = 0;
    $commas     = [];
    for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
      // Skip bracketed statements, like function calls.
      if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
        $i = $tokens[$i]['parenthesis_closer'];
        continue;
      }

      if ($tokens[$i]['code'] === T_COMMA) {
        // Before counting this comma, make sure we are not
        // at the end of the array.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $arrayEnd, true);
        if ($next !== false) {
          $valueCount++;
          $commas[] = $i;
        }
        else {
          // There is a comma at the end of a single line array.
          $error = 'Comma not allowed after last value in single-line array declaration';
          $phpcsFile->addError($error, $i, 'CommaAfterLast');
        }
      } // if COMMA
    } // for arrayStart -> arrayEnd

    $nextArrow = $arrayStart;
    while (($nextArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, ($nextArrow + 1), $arrayEnd)) !== false) {
      $this->_ensureOneSpaceAround($phpcsFile, $nextArrow, 'double arrow', 'Arrow');
    }

    foreach ($commas as $comma) {
      $this->_ensureOneSpaceAfter($phpcsFile, $comma, 'comma', 'Comma');
      $this->_ensureNoSpaceBefore($phpcsFile, $comma, 'comma', 'Comma');
    } // foreach commas

  } // _handleSingleLineArray

  private function _isNestedComma($tokens, $stackPtr, $nextToken) {

    $stackPtrCount = ( isset($tokens[$stackPtr]['nested_parenthesis']) )
                     ? count($tokens[$stackPtr]['nested_parenthesis'])
                     : 0;

    $nextPtrCount = ( isset($tokens[$nextToken]['nested_parenthesis']) )
                    ? count($tokens[$nextToken]['nested_parenthesis'])
                    : 0;

    // This comma is inside more parenthesis than the ARRAY keyword,
    // then there it is actually a comma used to separate arguments
    // in a function call.
    return $nextPtrCount > $stackPtrCount;

  } // _isNestedComma

  private function _ensureAlignment($phpcsFile, $token, $expected_spaces, $errCode, $content_type) {

    $tokens = $phpcsFile->getTokens();

    $actual_spaces = $tokens[$token]['column'] - 1;
    if ($actual_spaces === $expected_spaces) {
      return false;
    }

    $error      = $content_type . ' not aligned correctly; expected %s spaces but found %s';
    $data       = [$expected_spaces, $actual_spaces];
    $should_fix = $phpcsFile->addFixableError($error, $token, $errCode, $data);
    if ($should_fix) {
      $spaces_string = str_repeat(' ', $expected_spaces);
      if ($actual_spaces === 0) {
        $phpcsFile->fixer->replaceToken($token, $spaces_string . $tokens[$token]['content']);
      }
      else {
        $phpcsFile->fixer->replaceToken($token - 1, $spaces_string);
      }
    } // if shouldfix

    return true;

  } // _ensureAlignment

} // Behance_Sniffs_Arrays_ArrayDeclarationSniff
