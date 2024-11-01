<?php

namespace WPStack_Connect_Vendor\Aws\Handler\GuzzleV5;

use WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamDecoratorTrait;
use WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a Guzzle 5 Stream to a PSR-7 Stream.
 *
 * @codeCoverageIgnore
 */
class PsrStream implements \WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var GuzzleStreamInterface */
    private $stream;
    public function __construct(\WPStack_Connect_Vendor\GuzzleHttp\Stream\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
    public function rewind()
    {
        $this->stream->seek(0);
    }
    public function getContents()
    {
        return $this->stream->getContents();
    }
}
