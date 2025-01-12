<?php

namespace WPStack_Connect_Vendor\Aws\Crypto;

use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
interface AesStreamInterface extends \WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface
{
    /**
     * Returns an identifier recognizable by `openssl_*` functions, such as
     * `aes-256-cbc` or `aes-128-ctr`.
     *
     * @return string
     */
    public function getOpenSslName();
    /**
     * Returns an AES recognizable name, such as 'AES/GCM/NoPadding'.
     *
     * @return string
     */
    public function getAesName();
    /**
     * Returns the IV that should be used to initialize the next block in
     * encrypt or decrypt.
     *
     * @return string
     */
    public function getCurrentIv();
}
