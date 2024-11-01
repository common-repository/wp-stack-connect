<?php

namespace WPStack_Connect_Vendor\Aws\Token;

use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Interface used to provide interchangeable strategies for adding authorization
 * to requests using the various AWS signature protocols.
 */
interface TokenAuthorization
{
    /**
     * Adds the specified token to a request by adding the required headers.
     *
     * @param RequestInterface     $request     Request to sign
     * @param TokenInterface       $token       Token
     *
     * @return RequestInterface Returns the modified request.
     */
    public function authorizeRequest(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, \WPStack_Connect_Vendor\Aws\Token\TokenInterface $token);
}
