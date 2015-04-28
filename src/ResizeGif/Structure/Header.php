<?php

namespace grandt\ResizeGif\Structure;

use Exception;
use grandt\ResizeGif\Files\FileHandler;

/**
 * License: GNU LGPL 2.1.
 *
 * @author    A. Grandt <php@grandt.com>
 * @copyright 2015 A. Grandt
 * @license   GNU LGPL 2.1
 * @version   1.0.0
 */
class Header extends AbstractExtensionBlock {
    public $signature = "GIF";
    public $version = "89a";

    /**
     * @param FileHandler $fh
     * @throws Exception
     */
    public function decode($fh) {
        if ($fh->getRemainingBytes() < 6) {
            throw new Exception("Insufficient data. Need 6 bytes, got " . $fh->getRemainingBytes());
        }
        $this->signature = $fh->readData(3);
        $this->version = $fh->readData(3);
    }

    /**
     * @return string
     */
    public function encode() {
        return $this->signature . $this->version;
    }
}
