<?php
/**
 * Created by PhpStorm.
 * User: grandt
 * Date: 23-04-2015
 * Time: 19:26
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
class ApplicationExtension extends AbstractExtensionBlock {
    public $blockLabel = self::LABEL_APPLICATION;

    public $blockLength;
    public $applicationIdentifier;
    public $applicationAuthenticationCode;

    /**
     * @param FileHandler $fh
     */
    public function decode($fh) {
        $fh->seekForward(1); // skip block label.

        $this->blockLength = $fh->readByteUint(); // 11
        $this->applicationIdentifier = $fh->readData(8);
        $this->applicationAuthenticationCode = $fh->readData(3);

        $this->readDataSubBlocks($fh);

        while ($fh->peekByte() == "\x00") {
            $fh->seekForward(1);
        }
    }

    /**
     * @return string
     */
    public function encode() {
        return "\x21"
        . chr($this->blockLabel)
        . "\x0b" // 11
        . $this->applicationIdentifier
        . $this->applicationAuthenticationCode
        . $this->dataSubBlocks . "\x00";
    }
}
