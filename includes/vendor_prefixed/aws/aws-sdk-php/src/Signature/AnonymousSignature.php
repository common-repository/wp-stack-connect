<?php

namespace WPStack_Connect_Vendor\Aws\Signature;

use WPStack_Connect_Vendor\Aws\Credentials\CredentialsInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Provides anonymous client access (does not sign requests).
 */
class AnonymousSignature implements \WPStack_Connect_Vendor\Aws\Signature\SignatureInterface
{
    /**
     * /** {@inheritdoc}
     */
    public function signRequest(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, \WPStack_Connect_Vendor\Aws\Credentials\CredentialsInterface $credentials)
    {
        return $request;
    }
    /**
     * /** {@inheritdoc}
     */
    public function presign(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, \WPStack_Connect_Vendor\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = [])
    {
        return $request;
    }
}
