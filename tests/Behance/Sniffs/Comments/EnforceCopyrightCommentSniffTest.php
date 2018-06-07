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

class Behance_Sniffs_Comments_EnforceCopyrightCommentSniffTest extends AbstractSniffUnitTest {

  public function getErrorList($testFile) {

    if ($testFile === 'EnforceCopyrightCommentSniffTest.1.inc') {
        return [3 => 1, 5 => 1, 26 => 1, 47 => 1];
    }

    if ($testFile === 'EnforceCopyrightCommentSniffTest.2.inc') {
        return [1 => 1];
    }

    if ($testFile === 'EnforceCopyrightCommentSniffTest.3.inc') {
        return [];
    }

  }

  public function getWarningList($testFile) {

    return [];

  }

}
