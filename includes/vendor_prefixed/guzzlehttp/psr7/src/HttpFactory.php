<?php

declare (strict_types=1);
namespace WPStack_Connect_Vendor\GuzzleHttp\Psr7;

use WPStack_Connect_Vendor\Psr\Http\Message\RequestFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ServerRequestInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\UploadedFileInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\UriFactoryInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\UriInterface;
/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements \WPStack_Connect_Vendor\Psr\Http\Message\RequestFactoryInterface, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseFactoryInterface, \WPStack_Connect_Vendor\Psr\Http\Message\ServerRequestFactoryInterface, \WPStack_Connect_Vendor\Psr\Http\Message\StreamFactoryInterface, \WPStack_Connect_Vendor\Psr\Http\Message\UploadedFileFactoryInterface, \WPStack_Connect_Vendor\Psr\Http\Message\UriFactoryInterface
{
    public function createUploadedFile(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : \WPStack_Connect_Vendor\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createStream(string $content = '') : \WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface
    {
        return \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface
    {
        try {
            $resource = \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource) : \WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface
    {
        return \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \WPStack_Connect_Vendor\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface
    {
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createRequest(string $method, $uri) : \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface
    {
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request($method, $uri);
    }
    public function createUri(string $uri = '') : \WPStack_Connect_Vendor\Psr\Http\Message\UriInterface
    {
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Uri($uri);
    }
}
