<?php
/**
 * Generic_Sniffs_Files_LineEndingsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Files_LineEndingsSniff.
 *
 * Checks that end of line characters are correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.5.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Files_LineEndingsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                   'CSS',
                                  );

    /**
     * The valid EOL character.
     *
     * @var string
     */
    public $eolChar = '\n';


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $found = $phpcsFile->eolChar;
        $found = str_replace("\n", '\n', $found);
        $found = str_replace("\r", '\r', $found);

        if ($found !== $this->eolChar) {
            // Check for single line files without an EOL. This is a very special
            // case and the EOL char is set to \n when this happens.
            if ($found === '\n') {
                $tokens    = $phpcsFile->getTokens();
                $lastToken = ($phpcsFile->numTokens - 1);
                if ($tokens[$lastToken]['line'] === 1
                    && $tokens[$lastToken]['content'] !== "\n"
                ) {
                    return;
                }
            }

            $error    = 'End of line character is invalid; expected "%s" but found "%s"';
            $expected = $this->eolChar;
            $expected = str_replace("\n", '\n', $expected);
            $expected = str_replace("\r", '\r', $expected);
            $data     = array(
                         $expected,
                         $found,
                        );
            $phpcsFile->addError($error, $stackPtr, 'InvalidEOLChar', $data);
        }

    }//end process()


}//end class

?>
