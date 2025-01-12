<?php

namespace WPStack_Connect_Vendor\Aws\Crypto;

use WPStack_Connect_Vendor\Aws\Crypto\Cipher\CipherMethod;
use WPStack_Connect_Vendor\Aws\Crypto\Cipher\Cbc;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Stream;
/**
 * Legacy abstract encryption client. New workflows should use
 * AbstractCryptoClientV2.
 *
 * @deprecated
 * @internal
 */
abstract class AbstractCryptoClient
{
    public static $supportedCiphers = ['cbc', 'gcm'];
    public static $supportedKeyWraps = [\WPStack_Connect_Vendor\Aws\Crypto\KmsMaterialsProvider::WRAP_ALGORITHM_NAME];
    /**
     * Returns if the passed cipher name is supported for encryption by the SDK.
     *
     * @param string $cipherName The name of a cipher to verify is registered.
     *
     * @return bool If the cipher passed is in our supported list.
     */
    public static function isSupportedCipher($cipherName)
    {
        return \in_array($cipherName, self::$supportedCiphers);
    }
    /**
     * Returns an identifier recognizable by `openssl_*` functions, such as
     * `aes-256-cbc` or `aes-128-ctr`.
     *
     * @param string $cipherName Name of the cipher being used for encrypting
     *                           or decrypting.
     * @param int $keySize Size of the encryption key, in bits, that will be
     *                     used.
     *
     * @return string
     */
    protected abstract function getCipherOpenSslName($cipherName, $keySize);
    /**
     * Constructs a CipherMethod for the given name, initialized with the other
     * data passed for use in encrypting or decrypting.
     *
     * @param string $cipherName Name of the cipher to generate for encrypting.
     * @param string $iv Base Initialization Vector for the cipher.
     * @param int $keySize Size of the encryption key, in bits, that will be
     *                     used.
     *
     * @return CipherMethod
     *
     * @internal
     */
    protected abstract function buildCipherMethod($cipherName, $iv, $keySize);
    /**
     * Performs a reverse lookup to get the openssl_* cipher name from the
     * AESName passed in from the MetadataEnvelope.
     *
     * @param $aesName
     *
     * @return string
     *
     * @internal
     */
    protected abstract function getCipherFromAesName($aesName);
    /**
     * Dependency to provide an interface for building an encryption stream for
     * data given cipher details, metadata, and materials to do so.
     *
     * @param Stream $plaintext Plain-text data to be encrypted using the
     *                          materials, algorithm, and data provided.
     * @param array $cipherOptions Options for use in determining the cipher to
     *                             be used for encrypting data.
     * @param MaterialsProvider $provider A provider to supply and encrypt
     *                                    materials used in encryption.
     * @param MetadataEnvelope $envelope A storage envelope for encryption
     *                                   metadata to be added to.
     *
     * @return AesStreamInterface
     *
     * @internal
     */
    public abstract function encrypt(\WPStack_Connect_Vendor\GuzzleHttp\Psr7\Stream $plaintext, array $cipherOptions, \WPStack_Connect_Vendor\Aws\Crypto\MaterialsProvider $provider, \WPStack_Connect_Vendor\Aws\Crypto\MetadataEnvelope $envelope);
    /**
     * Dependency to provide an interface for building a decryption stream for
     * cipher text given metadata and materials to do so.
     *
     * @param string $cipherText Plain-text data to be decrypted using the
     *                           materials, algorithm, and data provided.
     * @param MaterialsProviderInterface $provider A provider to supply and encrypt
     *                                             materials used in encryption.
     * @param MetadataEnvelope $envelope A storage envelope for encryption
     *                                   metadata to be read from.
     * @param array $cipherOptions Additional verification options.
     *
     * @return AesStreamInterface
     *
     * @internal
     */
    public abstract function decrypt($cipherText, \WPStack_Connect_Vendor\Aws\Crypto\MaterialsProviderInterface $provider, \WPStack_Connect_Vendor\Aws\Crypto\MetadataEnvelope $envelope, array $cipherOptions = []);
}
