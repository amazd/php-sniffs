<?php
class Behance_Sniffs_Functions_FunctionMbStringSniff implements PHP_CodeSniffer_Sniff {

  const MBSTRING_FUNCTION_MAP = [
      "strlen"        => "mb_strlen",
      "strpos"        => "mb_strpos",
      "strrpos"       => "mb_strrpos",
      "substr"        => "mb_substr",
      "strtolower"    => "mb_strtolower",
      "strtoupper"    => "mb_strtoupper",
      "stripos"       => "mb_stripos",
      "strripos"      => "mb_strripos",
      "strstr"        => "mb_strstr",
      "stristr"       => "mb_stristr",
      "strrchr"       => "mb_strrchr",
      "substr_count"  => "mb_substr_count",
      "ereg"          => "mb_ereg",
      "eregi"         => "mb_eregi",
      "ereg_replace"  => "mb_ereg_replace",
      "eregi_replace" => "mb_eregi_replace",
      "split"         => "mb_split"
  ];

  /*
   * Returns the token types that this sniff is interested in.
   *
   * @return array(int)
   */
  public function register() {

    return [ T_STRING ];

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

    $tokens          = $phpcsFile->getTokens();
    $token_content   = $tokens[ $stackPtr ]['content'];
    $mb_function_map = self::MBSTRING_FUNCTION_MAP;

    if ( isset( $mb_function_map[ $token_content ] ) ) {

      $correction = $mb_function_map[ $token_content ];

      $fix = $phpcsFile->addFixableError( "Non multibyte string functions are not allowed, use {$correction}", $stackPtr, "InvalidStringFunction" );

      if ( $fix ) {
        $phpcsFile->fixer->replaceToken( $stackPtr, $correction );
      }

    } // if in_array

  } // process

} // Behance_Sniffs_Functions_FunctionMbStringSniff
