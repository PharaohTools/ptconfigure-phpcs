<?php
/**
 * PSR2_Sniffs_Namespaces_UseDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PSR2_Sniffs_Namespaces_UseDeclarationSniff.
 *
 * Ensures USE blocks are declared correctly.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.5.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR2_Sniffs_Namespaces_UseDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_USE);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->_shouldIgnoreUse($phpcsFile, $stackPtr) === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // Only one USE declaration allowed per statement.
        $next = $phpcsFile->findNext(array(T_COMMA, T_SEMICOLON), ($stackPtr + 1));
        if ($tokens[$next]['code'] === T_COMMA) {
            $error = 'There must be one USE keyword per declaration';
            $phpcsFile->addError($error, $stackPtr, 'MultipleDeclarations');
        }

        // Make sure this USE comes after the first namespace declaration.
        $prev = $phpcsFile->findPrevious(T_NAMESPACE, ($stackPtr - 1));
        if ($prev !== false) {
            $first = $phpcsFile->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $error = 'USE declarations must go after the first namespace declaration';
                $phpcsFile->addError($error, $stackPtr, 'UseAfterNamespace');
            }
        }

        // Only interested in the last USE statement from here onwards.
        $nextUse = $phpcsFile->findNext(T_USE, ($stackPtr + 1));
        while ($this->_shouldIgnoreUse($phpcsFile, $nextUse) === true) {
            $nextUse = $phpcsFile->findNext(T_USE, ($nextUse + 1));
            if ($nextUse === false) {
                break;
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $end  = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        $next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);
        $diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
        if ($diff !== 1) {
            if ($diff < 0) {
                $diff = 0;
            }

            $error = 'There must be one blank line after the last USE statement; %s found;';
            $data  = array($diff);
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterLastUse', $data);
        }

    }//end process()


    /**
     * Check if this use statement is part of the namespace block.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    private function _shouldIgnoreUse(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, T_CLASS) === true) {
            return true;
        }

        return false;

    }//end _shouldIgnoreUse()


}//end class


?>
