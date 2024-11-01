<?php

namespace WPStack_Connect_Vendor\Aws\Api\Serializer;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Aws\EndpointV2\EndpointProviderV2;
use WPStack_Connect_Vendor\Aws\EndpointV2\EndpointV2SerializerTrait;
use WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface;
/**
 * Prepares a JSON-RPC request for transfer.
 * @internal
 */
class JsonRpcSerializer
{
    use EndpointV2SerializerTrait;
    /** @var JsonBody */
    private $jsonFormatter;
    /** @var string */
    private $endpoint;
    /** @var Service */
    private $api;
    /** @var string */
    private $contentType;
    /**
     * @param Service  $api           Service description
     * @param string   $endpoint      Endpoint to connect to
     * @param JsonBody $jsonFormatter Optional JSON formatter to use
     */
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api, $endpoint, \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody $jsonFormatter = null)
    {
        $this->endpoint = $endpoint;
        $this->api = $api;
        $this->jsonFormatter = $jsonFormatter ?: new \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody($this->api);
        $this->contentType = \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody::getContentType($api);
    }
    /**
     * When invoked with an AWS command, returns a serialization array
     * containing "method", "uri", "headers", and "body" key value pairs.
     *
     * @param CommandInterface $command Command to serialize into a request.
     * @param $endpointProvider Provider used for dynamic endpoint resolution.
     * @param $clientArgs Client arguments used for dynamic endpoint resolution.
     *
     * @return RequestInterface
     */
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $command, $endpointProvider = null, $clientArgs = null)
    {
        $operationName = $command->getName();
        $operation = $this->api->getOperation($operationName);
        $commandArgs = $command->toArray();
        $headers = ['X-Amz-Target' => $this->api->getMetadata('targetPrefix') . '.' . $operationName, 'Content-Type' => $this->contentType];
        if ($endpointProvider instanceof \WPStack_Connect_Vendor\Aws\EndpointV2\EndpointProviderV2) {
            $this->setRequestOptions($endpointProvider, $command, $operation, $commandArgs, $clientArgs, $headers);
        }
        return new \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Request($operation['http']['method'], $this->endpoint, $headers, $this->jsonFormatter->build($operation->getInput(), $commandArgs));
    }
}
