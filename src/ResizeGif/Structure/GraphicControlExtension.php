<?php

namespace grandt\ResizeGif\Structure;


use com\grandt\BinStringStatic;
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
class GraphicControlExtension extends AbstractExtensionBlock {
    public $blockLabel = self::LABEL_GRAPHICS_CONTROL;

    public $blockLength;
    public $reserved;
    public $disposalMethod;
    public $userInputFlag;
    public $transparentColorFlag;
    public $delayTime;
    public $transparentColorIndex;
    /**
     * @var ImageDescriptor The image Data
     */
    public $imageDescriptor;

    /**
     * @return string
     */
    public function encode() {
        $packedFields = 0;
        $packedFields |= ($this->reserved << 5) & 0x70;
        $packedFields |= ($this->disposalMethod << 2) & 0x1c;
        $packedFields |= $this->userInputFlag ? 0x02 : 0x00;
        $packedFields |= $this->transparentColorFlag ? 0x01 : 0x00;

        return "\x21" . chr($this->blockLabel) . "\x04"
        . chr($packedFields & 0xff)
        . DataHandler::packUint16($this->delayTime)
        . chr($this->transparentColorIndex)
        . "\x00"
        . $this->imageDescriptor->encode();
    }

    /**
     * @param FileHandler $fh
     */
    public function decode($fh) {
        $fh->seekForward(1); // skip block label.
        $this->blockLength = $fh->readByteUint(); // 4
        $packedFields = $fh->readByteUint();
        $this->reserved = ($packedFields & 0x70) >> 5;
        $this->disposalMethod = ($packedFields & 0x1c) >> 2;
        $this->userInputFlag = ($packedFields & 0x02) > 0;
        $this->transparentColorFlag = ($packedFields & 0x01) > 0;
        $this->delayTime = $fh->readUint16();
        $this->transparentColorIndex = $fh->readByteUint();

        // Spool past the \x00 terminator byte. There *should* only be one.
        while ($fh->compareByte("\x00")) {
            $fh->readByte();
        }

        // The GCE will always be followed by the Image Descriptor.
        $this->imageDescriptor = new ImageDescriptor($fh);
        $this->imageDescriptor->parentGCE = $this;
    }

    /**
     *
     * @param LogicalScreenDescriptor $lsd
     * @return array|bool array of colors or FALSE
     */
    public function getTransparancyColor($lsd = null) {
        if (!$this->transparentColorFlag) {
            return FALSE;
        }
        if ($this->imageDescriptor->colorTableFlag
            && $this->imageDescriptor->colorTableSize > 0
            && BinStringStatic::_strlen($this->imageDescriptor->colorTable) > ($this->transparentColorIndex * 3)
        ) {
            $ct = $this->imageDescriptor->colorTable;
        } else {
            $ct = $lsd->colorTable;
        }

        if (BinStringStatic::_strlen($this->imageDescriptor->colorTable) > ($this->transparentColorIndex * 3)) {
            $color = array();
            $color['red'] = $ct[$this->transparentColorIndex * 3];
            $color['green'] = $ct[($this->transparentColorIndex * 3) + 1];
            $color['blue'] = $ct[($this->transparentColorIndex * 3) + 2];

            return $color;
        }
        return FALSE;
    }

    public function __clone() {
        $nGce = new GraphicControlExtension();
        $nGce->blockLength = $this->blockLength;
        $nGce->reserved = $this->reserved;
        $nGce->disposalMethod = $this->disposalMethod;
        $nGce->userInputFlag = $this->userInputFlag;
        $nGce->transparentColorFlag = $this->transparentColorFlag;
        $nGce->delayTime = $this->delayTime;
        $nGce->transparentColorIndex = $this->transparentColorIndex;

        $nGce->imageDescriptor = clone $this->imageDescriptor;
        return $nGce;
    }
}
