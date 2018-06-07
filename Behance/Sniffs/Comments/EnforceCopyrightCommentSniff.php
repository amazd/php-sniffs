<?php

/*************************************************************************
* ADOBE CONFIDENTIAL
* ___________________
*
* Copyright 2018 Adobe
* All Rights Reserved.
*
* NOTICE: All information contained herein is, and remains
* the property of Adobe and its suppliers, if any. The intellectual
* and technical concepts contained herein are proprietary to Adobe
* and its suppliers and are protected by all applicable intellectual
* property laws, including trade secret and copyright laws.
* Dissemination of this information or reproduction of this material
* is strictly forbidden unless prior written permission is obtained
* from Adobe.
**************************************************************************/

class Behance_Sniffs_Comments_EnforceCopyrightCommentSniff implements PHP_CodeSniffer_Sniff {

  private $_copyright_block = "/*************************************************************************
* ADOBE CONFIDENTIAL
* ___________________
*
* Copyright %s Adobe
* All Rights Reserved.
*
* NOTICE: All information contained herein is, and remains
* the property of Adobe and its suppliers, if any. The intellectual
* and technical concepts contained herein are proprietary to Adobe
* and its suppliers and are protected by all applicable intellectual
* property laws, including trade secret and copyright laws.
* Dissemination of this information or reproduction of this material
* is strictly forbidden unless prior written permission is obtained
* from Adobe.
**************************************************************************/";

  private $_block_signature = "/\* ADOBE CONFIDENTIAL/";
  private $_end_block_signature = "/\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*\*/";

  /**
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [T_OPEN_TAG];

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

    $this->_tokens = $phpcsFile->getTokens();

    $exclude = true;
    $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, $exclude);

    if ($this->_tokens[$next]['type'] !== 'T_COMMENT'
    || !preg_match($this->_block_signature, $this->_tokens[$next + 1]['content'])) {
  
      $error = 'No Adobe Copyright block found at the top of the file!';
      $should_fix = $phpcsFile->addFixableError($error, $next, 'missingCopyrightBlock ');
      if ($should_fix) {
        $phpcsFile->fixer->addContentBefore($stackPtr + 1, $phpcsFile->eolChar . sprintf($this->_copyright_block, date('Y')) . $phpcsFile->eolChar);
      }
    }

    $next_comment = $next;
    $block_num_of_lines = mb_substr_count($this->_copyright_block, "\n");

    while (($next_comment = $phpcsFile->findNext(T_COMMENT, ($next_comment + 1), null)) !== false) {
      if (preg_match($this->_block_signature, $this->_tokens[$next_comment + 1]['content'])
       && preg_match($this->_end_block_signature, $this->_tokens[$next_comment + $block_num_of_lines]['content'])) {
    
        $error = 'Duplicate Copyright block found!';
        $should_fix = $phpcsFile->addFixableError($error, $next_comment, 'duplicateCopyrightBlock');
        if ($should_fix) {
          $this->_removeDuplicateBlock($phpcsFile, $next_comment);
        }
      }
    }

    return (count($phpcsFile->getTokens()) + 1); // skips checking on the rest of the file

  }

  private function _removeDuplicateBlock($phpcsFile, $start_index) {

    $next_comment = $start_index;
    while (($next_comment = $phpcsFile->findNext(T_COMMENT, $next_comment, null)) !== false) {

      $phpcsFile->fixer->replaceToken($next_comment, '');
      $next_comment++;
    }

  }

}
