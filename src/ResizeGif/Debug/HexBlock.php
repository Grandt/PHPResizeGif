<?php

namespace grandt\ResizeGif\Debug;

/**
 * Open, and dump details of a GIF file.
 *
 * License: GNU LGPL 2.1.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015 A. Grandt
 * @license   GNU LGPL 2.1
 * @version   1.0.0
 */
class HexBlock {

    /**
     * Print a HexBlock from the current position in the file stream.
     *
     * The pointer will be returned to the initial position when done, essentially leaving the file stream untouched.
     *
     * @param resource $handle
     * @param int $bytes number of bytes to print.
     * @param bool $encodeHTML Encode the special characters that may occur on the right panel.
     */
    public static function printBlock($handle, $bytes, $encodeHTML = TRUE) {
        $pos = ftell($handle);
        $endPos = $pos + $bytes;
        $realPos = $pos & 0xfffffff0;
        fseek($handle, $realPos, SEEK_SET);

        $stat = fstat($handle);
        $length = $stat['size'];

        $isRangeOverreached = ($pos + $bytes) > $length;

        $rangeEnd = strlen(dechex($isRangeOverreached ? $length : $pos + $bytes));
        $rDigits = $rangeEnd % 2 == 1 ? $rangeEnd + 1 : $rangeEnd;

        $lengthEnd = strlen(dechex($bytes));
        $lDigits = $lengthEnd % 2 == 1 ? $lengthEnd + 1 : $lengthEnd;


        echo "Start: 0x" . str_pad(strtoupper(dechex($pos)), $rDigits, "0", STR_PAD_LEFT);
        echo "; Length: " . $bytes . " (0x" . str_pad(strtoupper(dechex($bytes)), $lDigits, "0", STR_PAD_LEFT) . ")\n";
        if ($isRangeOverreached) {
            $or = $pos + $bytes - $length;
            echo "* Requested length overreach by " . $or . " bytes, only " . ($bytes - $or) . " available in stream.\n";
            if ($bytes - $or <= 0) {
                fseek($handle, $pos, SEEK_SET);
                return;
            }
        }

        while (ftell($handle) < $endPos && !(feof($handle) || ftell($handle) === $length)) {
            $lsp = ftell($handle);

            echo str_pad(strtoupper(dechex($lsp)), $rDigits, "0", STR_PAD_LEFT) . ": ";
            $rp = "| ";

            for ($j = 0; $j < 16; $j++) {
                if ($j == 8) {
                    $rp .= " ";
                    echo " ";
                }
                if (($lsp + $j) < $pos) {
                    $rp .= "-";
                    echo "-- ";
                    fread($handle, 1);
                } elseif (($lsp + $j) >= $endPos || (feof($handle) || ftell($handle) === $length)) {
                    $rp .= "-";
                    echo "-- ";
                } else {
                    $d = fread($handle, 1);
                    echo bin2hex($d) . " ";

                    $od = ord($d);
                    if ($od < 0x20 || $od >= 0x7f) {
                        $d = '.';
                    }
                    $rp .= $encodeHTML ? htmlspecialchars($d) : $d;
                }
            }

            echo $rp . "\n";
        }
        echo "\n";
        fseek($handle, $pos, SEEK_SET);
    }
}
