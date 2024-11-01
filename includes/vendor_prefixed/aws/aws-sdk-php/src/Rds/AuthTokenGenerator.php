<?php

namespace WPStack_Connect_Vendor\Aws\Rds;

use WPStack_Connect_Vendor\Aws\Credentials\CredentialsInterface;
use WPStack_Connect_Vendor\Aws\Credentials\Credentials;
use WPStack_Connect_Vendor\Aws\Signature\SignatureV4;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Uri;
use WPStack_Connect_Vendor\GuzzleHttp\Promise;
use WPStack_Connect_Vendor\Aws;
/**
 * Generates RDS auth tokens for use with IAM authentication.
 */
class AuthTokenGenerator
{
    private $credentialProvider;
    /**
     * The constructor takes an instance of Credentials or a CredentialProvider
     *
     * @param callable|Credentials $creds
     */
    public function __construct($creds)
    {
        if ($creds instanceof \WPStack_Connect_Vendor\Aws\Credentials\CredentialsInterface) {
            $promise = new \WPStack_Connect_Vendor\GuzzleHttp\Promise\FulfilledPromise($creds);
            $this->credentialProvider = \WPStack_Connect_Vendor\Aws\constantly($promise);
        } else {
            $this->credentialProvider = $creds;
        }
    }
    /**
     * Create the token for database login
     *
     * @param string $endpoint The database hostname with port number specified
     *                         (e.g., host:port)
     * @param string $region The region where the database is located
     * @param string $username The username to login as
     * @param int $lifetime The lifetime of the token in minutes
     *
     * @return string Token generated
     */
    public function createToken($endpoint, $region, $username, $lifetime = 15)
    {
        if (!\is_numeric($lifetime) || $lifetime > 15 || $lifetime <= 0) {
            throw new \InvalidArgumentException("Lifetime must be a positive number less than or equal to 15, was {$lifetime}", null);
        }
        $uri = new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Uri($endpoint);
        $uri = $uri->withPath('/');
        $uri = $uri->withQuery('Action=connect&DBUser=' . $username);
        $request = new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request('GET', $uri);
        $signer = new \WPStack_Connect_Vendor\Aws\Signature\SignatureV4('rds-db', $region);
        $provider = $this->credentialProvider;
        $url = (string) $signer->presign($request, $provider()->wait(), '+' . $lifetime . ' minutes')->getUri();
        // Remove 2 extra slash from the presigned url result
        return \substr($url, 2);
    }
}
