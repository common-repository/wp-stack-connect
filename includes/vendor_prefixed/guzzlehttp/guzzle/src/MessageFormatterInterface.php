<?php

namespace WPStack_Connect_Vendor\GuzzleHttp;

use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
interface MessageFormatterInterface
{
    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface       $request  Request that was sent
     * @param ResponseInterface|null $response Response that was received
     * @param \Throwable|null        $error    Exception that was received
     */
    public function format(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, ?\WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response = null, ?\Throwable $error = null) : string;
}
