<?php

namespace WPStack_Connect_Vendor\Aws;

use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
interface ResponseContainerInterface
{
    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse();
}
