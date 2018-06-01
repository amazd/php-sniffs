<?php
class Behance_Sniffs_Arrays_ArrayDeclarationSniff implements PHP_CodeSniffer_Sniff {

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
    $isEmpty      = false;

    // Check for empty arrays.
    $content     = $phpcsFile->findNext([T_WHITESPACE], ($arrayStart + 1), ($arrayEnd + 1), true);
    $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), $arrayStart, true);
    if ($content === $arrayEnd) {
      $isEmpty = true;
      // Empty array, but if the brackets aren't together, there's a problem.
      if (($arrayEnd - $arrayStart) !== 1) {
        $error      = 'Empty array declaration must have no spaces';
        $should_fix = $phpcsFile->addFixableError($error, $arrayStart + 1, 'SpaceInEmptyArray');
        if ($should_fix) {
          $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
        }

        // We can return here because there is nothing else to check. All code
        // below can assume that the array is not empty.
        return;
      } // if arrayEnd - arrayStart
    } // if content = arrayEnd

    if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {

      if (!$isEmpty) {
        // ensure whitespace after start and before close
        if ($arrayStart + 1 !== $content) {
          $error      = 'Expected no space after array open';
          $should_fix = $phpcsFile->addFixableError($error, $arrayStart + 1, 'SpaceAfterArrayOpen');
          if ($should_fix) {
            $phpcsFile->fixer->replaceToken($arrayStart + 1, '');
          }
        } // if space after open

        if ($arrayEnd - 1 !== $lastContent) {
          $error      = 'Expected no space space before array close';
          $should_fix = $phpcsFile->addFixableError($error, $arrayEnd - 1, 'SpaceBeforeArrayClose');
          if ($should_fix) {
            $phpcsFile->fixer->replaceToken($arrayEnd - 1, '');
          }
        } // if space after close
      } // if !isEmpty

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

      // Now check each of the double arrows (if any).
      $nextArrow = $arrayStart;
      while (( $nextArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, ( $nextArrow + 1 ), $arrayEnd) ) !== false) {
        $this->_ensureArrowSpacing($phpcsFile, $nextArrow);
      } // while nextArrow

      if ($valueCount > 0) {
        // We have a multiple value array that is inside a condition or
        // function. Check its spacing is correct.
        foreach ($commas as $comma) {
          if ($tokens[$comma + 1]['code'] !== T_WHITESPACE) {
            $content    = $tokens[$comma + 1]['content'];
            $error      = 'Expected 1 space between comma and "%s"';
            $data       = [$content];
            $should_fix = $phpcsFile->addFixableError($error, $comma, 'NoSpaceAfterComma', $data);
            if ($should_fix) {
              $phpcsFile->fixer->addContent($comma, ' ');
            }
          } // if comma + 1 !== T_WHITESPACE
          elseif ($tokens[$comma + 1]['content'] !== ' ' && $tokens[$comma + 1]['content'] !== $phpcsFile->eolChar) {
            $content    = $tokens[$comma + 2]['content'];
            $error      = 'Expected 1 space between comma and "%s"';
            $data       = [$content];
            $should_fix = $phpcsFile->addFixableError($error, $comma, 'NoSpaceAfterComma', $data);
            if ($should_fix) {
              $phpcsFile->fixer->replaceToken($comma + 1, ' ');
            }
          } // elseif comma + 1 !== T_WHITESPACE

          $this->_ensureNoSpaceBefore($phpcsFile, $comma, 'SpaceBeforeComma', 'comma');
        } // foreach commas
      } // if valueCount

      return;
    } // if arrayStart line === arrayEnd line

    // Check the closing bracket is on a new line.
    if ($tokens[$lastContent]['line'] == ($tokens[$arrayEnd]['line'])) {
      $error      = 'Closer of array declaration must be on a new line';
      $should_fix = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNewLine');
      if ($should_fix) {
        $phpcsFile->fixer->replaceToken($arrayEnd, $phpcsFile->eolChar . $tokens[$arrayEnd]['content']);
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
            $this->_ensureNoSpaceBefore($phpcsFile, $nextToken, 'SpaceBeforeComma', 'comma');
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

        $this->_ensureArrowSpacing($phpcsFile, $nextToken);

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
            $phpcsFile->fixer->replaceToken($index['value'], $phpcsFile->eolChar . $tokens[$index['value']]['content']);
          }
        } // if value is on line with bracket

        continue;
      } // if index[index]

      if ($tokens[$index['index']]['line'] === $tokens[$stackPtr]['line']) {
        $error      = 'The first index in a multi-value array must be on a new line';
        $should_fix = $phpcsFile->addFixableError($error, $index['index'], 'FirstIndexNoNewline');
        if ($should_fix) {
          $phpcsFile->fixer->replaceToken($index['index'], $phpcsFile->eolChar . $tokens[$index['index']]['content']);
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
          $this->_ensureNoSpaceBefore($phpcsFile, $nextComma, 'SpaceBeforeComma', 'comma');
        } // if nextComma !false
      } // if !isArrayOpener
    } // foreach indices

  } // process

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

  private function _ensureNoSpaceBefore($phpcsFile, $token, $errCode, $content_type) {

    $tokens = $phpcsFile->getTokens();

    if ($tokens[$token - 1]['code'] !== T_WHITESPACE) {
      return false;
    }

    $content     = $tokens[$token - 2]['content'];
    $spaceLength = mb_strlen($tokens[$token - 1]['content']);
    $error       = 'Expected 0 spaces between "%s" and %s; %s found';
    $data        = [$content, $content_type, $spaceLength];
    $should_fix  = $phpcsFile->addFixableError($error, $token - 1, $errCode, $data);
    if ($should_fix) {
      $phpcsFile->fixer->replaceToken($token - 1, '');
    }

    return true;

  } // _ensureNoSpaceBefore

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

  private function _ensureArrowSpacing($phpcsFile, $arrow) {

    $tokens = $phpcsFile->getTokens();

    $beforeContent = $tokens[$arrow - 1]['content'];
    if ($beforeContent !== ' ') {
      $error      = 'Expected 1 space between "%s" and double arrow';
      $should_fix = $phpcsFile->addFixableError($error, $arrow, 'NoSpaceBeforeDoubleArrow', [$beforeContent]);
      if ($should_fix) {
        if ($tokens[$arrow - 1]['code'] !== T_WHITESPACE) {
          $phpcsFile->fixer->addContentBefore($arrow, ' ');
        }
        else {
          $phpcsFile->fixer->replaceToken($arrow - 1, ' ');
        }
      } // if should_fix
    } // if nextArrow - 1 !== T_WHITESPACE

    $afterContent = $tokens[$arrow + 1]['content'];
    if ($afterContent !== ' ') {
      $error      = 'Expected 1 space between double arrow and "%s"';
      $should_fix = $phpcsFile->addFixableError($error, $arrow, 'NoSpaceAfterDoubleArrow', [$afterContent]);
      if ($should_fix) {
        if ($tokens[$arrow + 1]['code'] !== T_WHITESPACE) {
          $phpcsFile->fixer->addContent($arrow, ' ');
        }
        else {
          $phpcsFile->fixer->replaceToken($arrow + 1, ' ');
        }
      } // if should_fix
    } // if nextArrow + 1 !== T_WHITESPACE

  } // _ensureArrowSpacing

} // Behance_Sniffs_Arrays_ArrayDeclarationSniff
