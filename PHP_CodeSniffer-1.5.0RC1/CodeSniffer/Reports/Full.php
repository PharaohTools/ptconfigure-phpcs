<?php
/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Full report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.5.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Full implements PHP_CodeSniffer_Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array   $report      Prepared report data.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed line width.
     *
     * @return boolean
     */
    public function generateFileReport(
        $report,
        $showSources=false,
        $width=80
    ) {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        $width = max($width, 70);
        $file  = $report['filename'];

        echo PHP_EOL.'FILE: ';
        if (strlen($file) <= ($width - 9)) {
            echo $file;
        } else {
            echo '...'.substr($file, (strlen($file) - ($width - 9)));
        }

        echo PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        echo 'FOUND '.$report['errors'].' ERROR(S) ';
        if ($report['warnings'] > 0) {
            echo 'AND '.$report['warnings'].' WARNING(S) ';
        }

        echo 'AFFECTING '.count($report['messages']).' LINE(S)'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        // Work out the max line number for formatting.
        $maxLine = 0;
        foreach ($report['messages'] as $line => $lineErrors) {
            if ($line > $maxLine) {
                $maxLine = $line;
            }
        }

        $maxLineLength = strlen($maxLine);

        // The length of the word ERROR or WARNING; used for padding.
        if ($report['warnings'] > 0) {
            $typeLength = 7;
        } else {
            $typeLength = 5;
        }

        // The padding that all lines will require that are
        // printing an error message overflow.
        $paddingLine2  = str_repeat(' ', ($maxLineLength + 1));
        $paddingLine2 .= ' | ';
        $paddingLine2 .= str_repeat(' ', $typeLength);
        $paddingLine2 .= ' | ';

        // The maximum amount of space an error message can use.
        $maxErrorSpace = ($width - strlen($paddingLine2) - 1);

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $message = $error['message'];
                    if ($showSources === true) {
                        $message .= ' ('.$error['source'].')';
                    }

                    // The padding that goes on the front of the line.
                    $padding  = ($maxLineLength - strlen($line));
                    $errorMsg = wordwrap(
                        $message,
                        $maxErrorSpace,
                        PHP_EOL.$paddingLine2
                    );

                    echo ' '.str_repeat(' ', $padding).$line.' | '.$error['type'];
                    if ($error['type'] === 'ERROR') {
                        if ($report['warnings'] > 0) {
                            echo '  ';
                        }
                    }

                    echo ' | '.$errorMsg.PHP_EOL;
                }//end foreach
            }//end foreach
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL.PHP_EOL;
        return true;

    }//end generateFileReport()


    /**
     * Prints all errors and warnings for each file processed.
     *
     * @param string  $cachedData    Any partial report data that was returned from
     *                               generateFileReport during the run.
     * @param int     $totalFiles    Total number of files processed during the run.
     * @param int     $totalErrors   Total number of errors found during the run.
     * @param int     $totalWarnings Total number of warnings found during the run.
     * @param boolean $showSources   Show sources?
     * @param int     $width         Maximum allowed line width.
     * @param boolean $toScreen      Is the report being printed to screen?
     *
     * @return void
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        echo $cachedData;

        if ($toScreen === true
            && PHP_CODESNIFFER_INTERACTIVE === false
            && class_exists('PHP_Timer', false) === true
        ) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

    }//end generate()


}//end class

?>
