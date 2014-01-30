<?php
class Behance_Sniffs_Classes_ClassNamingSniff implements PHP_CodeSniffer_Sniff {

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

    return [
        T_CLASS,
        T_INTERFACE,
        T_TRAIT
    ];

  } // register


  /**
   * Processes the tokens that this sniff is interested in.
   *
   * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
   * @param int                  $stackPtr  The position in the stack where
   *                                        the token was found.
   *
   * @return void
   */
  public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

    $constructName = $phpcsFile->getDeclarationName( $stackPtr );
    $constructPath = $phpcsFile->getFilename();
    $expectedFile  = str_replace( '_', DIRECTORY_SEPARATOR, $constructName );

    // "fuzzy search"
    $namePieces  = explode( DIRECTORY_SEPARATOR, $constructPath );
    $pieces      = preg_grep( '/[A-Z]/', $namePieces );
    $guess       = array_pop( $pieces );
    $res         = array_slice( $namePieces, array_search( $guess, $namePieces ) );
    $guessName   = basename( implode( '_', $res ), '.php' );

    if ( strpos( $constructPath, $expectedFile ) === false ) {;
      $warning = "Classname '{$constructName}' does not seem to follow conventions - expected the file path to resemble '{$expectedFile}' or the name to resemble '{$guessName}'";
      $phpcsFile->addError( $warning, $stackPtr, 'UnexpectedConstructName' );
    }

  } // process

} // Behance_Sniffs_Classes_ClassNamingSniff
