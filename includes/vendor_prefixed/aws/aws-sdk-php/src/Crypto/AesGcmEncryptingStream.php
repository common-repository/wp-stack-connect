<?php

namespace WPStack_Connect_Vendor\Aws\Crypto;

use WPStack_Connect_Vendor\Aws\Crypto\Polyfill\AesGcm;
use WPStack_Connect_Vendor\Aws\Crypto\Polyfill\Key;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\StreamDecoratorTrait;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
use RuntimeException;
/**
 * @internal Represents a stream of data to be gcm encrypted.
 */
class AesGcmEncryptingStream implements \WPStack_Connect_Vendor\Aws\Crypto\AesStreamInterface, \WPStack_Connect_Vendor\Aws\Crypto\AesStreamInterfaceV2
{
    use StreamDecoratorTrait;
    private $aad;
    private $initializationVector;
    private $key;
    private $keySize;
    private $plaintext;
    private $tag = '';
    private $tagLength;
    /**
     * Same as non-static 'getAesName' method, allowing calls in a static
     * context.
     *
     * @return string
     */
    public static function getStaticAesName()
    {
        return 'AES/GCM/NoPadding';
    }
    /**
     * @param StreamInterface $plaintext
     * @param string $key
     * @param string $initializationVector
     * @param string $aad
     * @param int $tagLength
     * @param int $keySize
     */
    public function __construct(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $plaintext, $key, $initializationVector, $aad = '', $tagLength = 16, $keySize = 256)
    {
        $this->plaintext = $plaintext;
        $this->key = $key;
        $this->initializationVector = $initializationVector;
        $this->aad = $aad;
        $this->tagLength = $tagLength;
        $this->keySize = $keySize;
    }
    public function getOpenSslName()
    {
        return "aes-{$this->keySize}-gcm";
    }
    /**
     * Same as static method and retained for backwards compatibility
     *
     * @return string
     */
    public function getAesName()
    {
        return self::getStaticAesName();
    }
    public function getCurrentIv()
    {
        return $this->initializationVector;
    }
    public function createStream()
    {
        if (\version_compare(\PHP_VERSION, '7.1', '<')) {
            return \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor(\WPStack_Connect_Vendor\Aws\Crypto\Polyfill\AesGcm::encrypt((string) $this->plaintext, $this->initializationVector, new \WPStack_Connect_Vendor\Aws\Crypto\Polyfill\Key($this->key), $this->aad, $this->tag, $this->keySize));
        } else {
            return \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor(\openssl_encrypt((string) $this->plaintext, $this->getOpenSslName(), $this->key, \OPENSSL_RAW_DATA, $this->initializationVector, $this->tag, $this->aad, $this->tagLength));
        }
    }
    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
    public function isWritable()
    {
        return \false;
    }
}
