<?php

namespace WPStack_Connect_Vendor\Aws\S3\Crypto;

use WPStack_Connect_Vendor\Aws\AwsClientInterface;
use WPStack_Connect_Vendor\Aws\Middleware;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
trait UserAgentTrait
{
    private function appendUserAgent(\WPStack_Connect_Vendor\Aws\AwsClientInterface $client, $agentString)
    {
        $list = $client->getHandlerList();
        $list->appendBuild(\WPStack_Connect_Vendor\Aws\Middleware::mapRequest(function (\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $req) use($agentString) {
            if (!empty($req->getHeader('User-Agent')) && !empty($req->getHeader('User-Agent')[0])) {
                $userAgent = $req->getHeader('User-Agent')[0];
                if (\strpos($userAgent, $agentString) === \false) {
                    $userAgent .= " {$agentString}";
                }
            } else {
                $userAgent = $agentString;
            }
            $req = $req->withHeader('User-Agent', $userAgent);
            return $req;
        }));
    }
}
