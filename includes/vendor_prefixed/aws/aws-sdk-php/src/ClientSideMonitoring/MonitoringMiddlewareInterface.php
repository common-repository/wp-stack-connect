<?php

namespace WPStack_Connect_Vendor\Aws\ClientSideMonitoring;

use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Aws\Exception\AwsException;
use WPStack_Connect_Vendor\Aws\ResultInterface;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * @internal
 */
interface MonitoringMiddlewareInterface
{
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param RequestInterface $request
     * @return array
     */
    public static function getRequestData(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request);
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param ResultInterface|AwsException|\Exception $klass
     * @return array
     */
    public static function getResponseData($klass);
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $cmd, \WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request);
}
