<?php

namespace WPStack_Connect_Vendor\Aws\Handler\GuzzleV5;

use WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamDecoratorTrait;
use WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a PSR-7 Stream to a Guzzle 5 Stream.
 *
 * @codeCoverageIgnore
 */
class GuzzleStream implements \WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var Psr7StreamInterface */
    private $stream;
    public function __construct(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
