<?php
/**
 * Created by PhpStorm.
 * User: grandt
 * Date: 23-04-2015
 * Time: 19:27
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
class CommentExtension extends AbstractExtensionBlock {
    public $blockLabel = self::LABEL_COMMENT;

    /**
     * @param FileHandler $fh
     */
    public function decode($fh) {
        $fh->seekForward(1); // skip block label.

        $this->readDataSubBlocks($fh);

        while ($fh->peekByte() == "\x00") {
            $fh->seekForward(1);
        }
    }

    /**
     * @return string
     */
    public function encode() {
        return "\x21" . chr($this->blockLabel) . $this->dataSubBlocks . "\x00";
    }
}
