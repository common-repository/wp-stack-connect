<?php

namespace WPStack_Connect_Vendor\GuzzleHttp\Handler;

use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
interface CurlFactoryInterface
{
    /**
     * Creates a cURL handle resource.
     *
     * @param RequestInterface $request Request
     * @param array            $options Transfer options
     *
     * @throws \RuntimeException when an option cannot be applied
     */
    public function create(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, array $options) : \WPStack_Connect_Vendor\GuzzleHttp\Handler\EasyHandle;
    /**
     * Release an easy handle, allowing it to be reused or closed.
     *
     * This function must call unset on the easy handle's "handle" property.
     */
    public function release(\WPStack_Connect_Vendor\GuzzleHttp\Handler\EasyHandle $easy) : void;
}
