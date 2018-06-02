<?php
abstract class Behance_AbstractSniff implements PHP_CodeSniffer_Sniff {

  protected function _ensureNoSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol = false) {

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
    $should_fix = $phpcsFile->addFixableError($error, $token - 1, 'NoSpaceBefore' . $code, $data);
    if ($should_fix) {
      $phpcsFile->fixer->replaceToken($token - 1, '');
    }

    return true;

  } // _ensureNoSpaceBefore

  protected function _ensureNoSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol = false) {

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
    $should_fix = $phpcsFile->addFixableError($error, $token + 1, 'NoSpaceAfter' . $code, $data);
    if ($should_fix) {
      $phpcsFile->fixer->replaceToken($token + 1, '');
    }

    return true;

  } // _ensureNoSpaceAfter

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
    } // if allow_eol

    $error = 'Expected 1 space between "%s" and %s';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'OneSpaceBefore' . $code, [$beforeContent, $name]);
    if ($should_fix) {
      if ($tokens[$token - 1]['code'] !== T_WHITESPACE) {
        $phpcsFile->fixer->addContentBefore($token, ' ');
      }
      else {
        $phpcsFile->fixer->replaceToken($token - 1, ' ');
      }
    } // if should_fix

    return true;

  } // _ensureOneSpaceBefore

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
    } // if allow_eol

    $error = 'Expected 1 space between %s and "%s"';
    $should_fix = $phpcsFile->addFixableError($error, $token, 'OneSpaceAfter' . $code, [$name, $afterContent]);
    if ($should_fix) {
      if ($tokens[$token + 1]['code'] !== T_WHITESPACE) {
        $phpcsFile->fixer->addContent($token, ' ');
      }
      else {
        $phpcsFile->fixer->replaceToken($token + 1, ' ');
      }
    } // if should_fix

    return true;

  } // _ensureOneSpaceAfter

  final protected function _ensureOneSpaceAround($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $this->_ensureOneSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol);
    $this->_ensureOneSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol);

  } // _ensureOneSpaceAround

  final protected function _ensureNoSpaceAround($phpcsFile, $token, $name, $code, $allow_eol = false) {

    $this->_ensureNoSpaceBefore($phpcsFile, $token, $name, $code, $allow_eol);
    $this->_ensureNoSpaceAfter($phpcsFile, $token, $name, $code, $allow_eol);

  } // _ensureNoSpaceAround

} // Behance_AbstractSniff
