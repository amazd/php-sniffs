<?php
if ( class_exists( 'PHP_CodeSniffer_Standards_AbstractPatternSniff', true ) === false ) {
    throw new PHP_CodeSniffer_Exception( 'Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found' );
}

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
  // @codingStandardsIgnoreStart
  // ignored because this is a PHPCS specific function
  // and '_' cannot be prefixed
  protected function getPatterns() {
  // @codingStandardsIgnoreEnd

        return [
            'try {EOL...}EOLcatch ( ... ) {EOL',
            'do {EOL...} while ( ... );EOL',
            'while ( ... ) {EOL',
            'for ( ... ) {EOL',
            'if ( ... ) {EOL',
            'foreach ( ... ) {EOL',
            '}EOLEOLelse if ( ... ) {EOL',
            '}EOLEOLelseif ( ... ) {EOL',
            '}EOLEOL...else {EOL',
        ];

  } // getPatterns

} // Behance_Sniffs_ControlStructures_ControlSignatureSniff
