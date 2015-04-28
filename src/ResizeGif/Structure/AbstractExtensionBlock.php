<?php
/**
 * Created by PhpStorm.
 * User: grandt
 * Date: 24-04-2015
 * Time: 07:32
 */

namespace grandt\ResizeGif\Structure;


use grandt\ResizeGif\Files\FileHandler;

/**
 * License: GNU LGPL 2.1.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015 A. Grandt
 * @license   GNU LGPL 2.1
 * @version   1.0.0
 */
abstract class AbstractExtensionBlock {
    public $ctSizeList = array(2 => 0, 4 => 1, 8 => 2, 16 => 3, 32 => 4, 64 => 5, 128 => 6, 256 => 7);
    public $ctSizeListRev = array(0 => 2, 1 => 4, 2 => 8, 3 => 16, 4 => 32, 5 => 64, 6 => 128, 7 => 256);

    const LABEL_APPLICATION = 0xff;
    const LABEL_COMMENT = 0xfe;
    const LABEL_GRAPHICS_CONTROL = 0xf9;
    const LABEL_PLAIN_TEXT = 0x01;

    const CONTROL_EXTENSION = 0x21;
    const CONTROL_IMAGE = 0x2c;
    const CONTROL_TRAILER = 0x3b;
    const CONTROL_TERMINATOR = 0x00;

    public $blockLabel;
    public $dataSubBlocks = "";

    /**
     * @param FileHandler $fh
     */
    public function __construct($fh = null) {
        if ($fh != null) {
            $this->decode($fh);
        }
    }

    /**
     * @return string
     */
    abstract public function encode();

    /**
     * @param FileHandler $fh
     */
    abstract public function decode($fh);

    /**
     * @param FileHandler $fh
     */
    public function encodeToFile($fh) {
        $fh->writeData($this->encode());
    }

    /**
     * @param FileHandler $fh
     */
    public function readDataSubBlocks($fh) {
        $this->dataSubBlocks = "";

        while (!$fh->compareByte("\x00")) {
            $subBlockLength = $fh->readByteUint();
            if ($subBlockLength == 0) {
                return;
            }
            $this->dataSubBlocks .= chr($subBlockLength) . $fh->readData($subBlockLength);
        }
    }
}
