<?php

namespace grandt\ResizeGif\Structure;

use grandt\ResizeGif\Files\DataHandler;
use grandt\ResizeGif\Files\FileHandler;

/**
 * License: GNU LGPL 2.1.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015 A. Grandt
 * @license   GNU LGPL 2.1
 * @version   1.0.0
 */
class PlainTextExtension extends AbstractExtensionBlock {
    public $blockLabel = self::LABEL_PLAIN_TEXT;

    public $blockLength;
    public $textGridLeftPosition;
    public $textGridTopPosition;
    public $textGridWidth;
    public $textGridHeight;
    public $characterCellWidth;
    public $characterCellHeight;
    public $textFGColorIndex;
    public $textBGColorIndex;

    /**
     * @param FileHandler $fh
     */
    public function decode($fh) {
        $fh->seekForward(1); // skip block label.

        $this->blockLength = $fh->readByteUint(); // 12
        $this->textGridLeftPosition = $fh->readUint16();
        $this->textGridTopPosition = $fh->readUint16();
        $this->textGridWidth = $fh->readUint16();
        $this->textGridHeight = $fh->readUint16();
        $this->characterCellWidth = $fh->readByteUint();
        $this->characterCellHeight = $fh->readByteUint();
        $this->textFGColorIndex = $fh->readByteUint();
        $this->textBGColorIndex = $fh->readByteUint();

        $this->readDataSubBlocks($fh);

        // Spool past the \x00 terminator byte. There *should* only be one.
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
        . "\x0c" // 12
        . DataHandler::packUint16($this->textGridLeftPosition)
        . DataHandler::packUint16($this->textGridTopPosition)
        . DataHandler::packUint16($this->textGridWidth)
        . DataHandler::packUint16($this->textGridHeight)
        . ord($this->characterCellWidth)
        . ord($this->characterCellHeight)
        . ord($this->textFGColorIndex)
        . ord($this->textBGColorIndex)
        . $this->dataSubBlocks . "\x00";
    }
}
