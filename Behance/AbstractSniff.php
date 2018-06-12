<?php
abstract class Behance_AbstractSniff implements PHP_CodeSniffer_Sniff {

  final protected function _ensureNoSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $tokens = $phpcsFile->getTokens();

    if ($tokens[$token - 1]['code'] !== T_WHITESPACE) {
      return false;
    }

    if ($allow_eol && $tokens[$token - 1]['content'] === $phpcsFile->eolChar) {
      return false;
    }

    $content = $tokens[$token - 2]['content'];
    $error = 'Expected 0 spaces between "%s" and %s;';
    $data = [$content, $name];
    $should_fix = $phpcsFile->addFixableError($error, $token, 'NoSpaceBefore' . $code, $data);
    if ($should_fix) {
      $phpcsFile->fixer->replaceToken($token - 1, '');
    }

    return true;

  }

  final protected function _ensureNoSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $tokens = $phpcsFile->getTokens();

    if ($tokens[$token + 1]['code'] !== T_WHITESPACE) {
      return false;
    }

    if ($allow_eol && $tokens[$token + 1]['content'] === $phpcsFile->eolChar) {
      return false;
    }

    $content = $tokens[$token + 2]['content'];
    $error = 'Expected 0 spaces between "%s" and %s;';
    $data = [$content, $name];
    $should_fix = $phpcsFile->addFixableError($error, $token, 'NoSpaceAfter' . $code, $data);
    if ($should_fix) {
      $phpcsFile->fixer->replaceToken($token + 1, '');
    }

    return true;

  }

  final protected function _ensureOneSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $tokens = $phpcsFile->getTokens();
    $beforeContent = $tokens[$token - 1]['content'];

    if ($beforeContent === ' ') {
      return false;
    }
    if ($allow_eol) {
      $beforeToken = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $token - 1, null, true);
      if ($tokens[$beforeToken]['line'] !== $tokens[$token]['line']) {
        return false;
      }
      $beforeContent = $tokens[$beforeToken]['content'];
    }

    $error = 'Expected 1 space between "%s" and %s';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'OneSpaceBefore' . $code, [$beforeContent, $name]);
    if ($should_fix) {
      if ($tokens[$token - 1]['code'] !== T_WHITESPACE) {
        $phpcsFile->fixer->addContentBefore($token, ' ');
      } else {
        $phpcsFile->fixer->replaceToken($token - 1, ' ');
      }
    }

    return true;

  }

  final protected function _ensureOneSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $tokens = $phpcsFile->getTokens();
    $afterContent = $tokens[$token + 1]['content'];

    if ($afterContent === ' ') {
      return false;
    }
    if ($allow_eol) {
      $afterToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $token + 1, null, true);
      if ($tokens[$afterToken]['line'] !== $tokens[$token]['line']) {
        return false;
      }
      $afterContent = $tokens[$afterToken]['content'];
    }

    $error = 'Expected 1 space between %s and "%s"';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'OneSpaceAfter' . $code, [$name, $afterContent]);
    if ($should_fix) {
      if ($tokens[$token + 1]['code'] !== T_WHITESPACE) {
        $phpcsFile->fixer->addContent($token, ' ');
      } else {
        $phpcsFile->fixer->replaceToken($token + 1, ' ');
      }
    }

    return true;

  }

  final protected function _ensureSameLine($phpcsFile, $tokenStart, $tokenEnd, $nameStart, $nameEnd, $code) {

    $tokens = $phpcsFile->getTokens();

    if ($tokens[$tokenStart]['line'] === $tokens[$tokenEnd]['line']) {
      return;
    }

    $error = 'Expected %s and %s to be on the same line';
    $should_fix = $phpcsFile->addFixableError($error, $tokenStart, 'SameLine' . $code, [$nameStart, $nameEnd]);
    if ($should_fix) {
      while (--$tokenEnd > $tokenStart) {
        $phpcsFile->fixer->replaceToken($tokenEnd, '');
      }
    }

    return true;

  }

  final protected function _ensureNewLineAfter($phpcsFile, $token, $name, $code, $expected_count = 1) {

    $tokens = $phpcsFile->getTokens();
    $afterToken = $phpcsFile->findNext(T_WHITESPACE, $token + 1, null, true);

    $actual_count = $tokens[$afterToken]['line'] - $tokens[$token]['line'];

    // only handle less than expected newlines
    if ($actual_count >= $expected_count) {
      return;
    }

    $error = 'Expected %s newline(s) after %s';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'Newline' . $code, [$expected_count, $name]);
    if ($should_fix) {
      while ($expected_count > $actual_count) {
        $phpcsFile->fixer->addContent($token, $phpcsFile->eolChar);
        $actual_count++;
      }
    }

  }

  final protected function _ensureNewLineBefore($phpcsFile, $token, $name, $code, $expected_count = 1) {

    $tokens = $phpcsFile->getTokens();
    $beforeToken = $phpcsFile->findPrevious(T_WHITESPACE, $token - 1, null, true);

    $actual_count = $tokens[$token]['line'] - $tokens[$beforeToken]['line'];

    // only handle less than expected newlines
    if ($actual_count >= $expected_count) {
      return;
    }

    $error = 'Expected %s newline(s) before %s';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'Newline' . $code, [$expected_count, $name]);
    if ($should_fix) {
      while ($expected_count > $actual_count) {
        $phpcsFile->fixer->addContentBefore($token, $phpcsFile->eolChar);
        $actual_count++;
      }
    }

  }

  final protected function _ensureOneSpaceAround($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $this->_ensureOneSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol);
    $this->_ensureOneSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol);

  }

  final protected function _ensureNoSpaceAround($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $this->_ensureNoSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol);
    $this->_ensureNoSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol);

  }

}
