<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @copyright Adobe
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   behance/php-sniffs
 * @author    Kevin Ran <kran@adobe.com>
 * @copyright Adobe
 * @license   Proprietary
 * @link      https://github.com/behance/php-sniffs
 */
class Behance_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff {

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];


    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
                'try {EOL...}EOLcatch ( ... ) {EOL',
                'do {EOL...} while ( ... );EOL',
                'while ( ... ) {EOL',
                'for ( ... ) {EOL',
                'if ( ... ) {EOL',
                'foreach ( ... ) {EOL',
                '}EOLEOLelse if ( ... ) {EOL',
                '}EOLEOLelseif ( ... ) {EOL',
                '}EOLEOLelse {EOL',
               );

    }//end getPatterns()


}//end class

