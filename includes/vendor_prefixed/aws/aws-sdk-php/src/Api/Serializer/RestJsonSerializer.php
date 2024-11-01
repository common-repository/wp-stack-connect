<?php

namespace WPStack_Connect_Vendor\Aws\Api\Serializer;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
/**
 * Serializes requests for the REST-JSON protocol.
 * @internal
 */
class RestJsonSerializer extends \WPStack_Connect_Vendor\Aws\Api\Serializer\RestSerializer
{
    /** @var JsonBody */
    private $jsonFormatter;
    /** @var string */
    private $contentType;
    /**
     * @param Service  $api           Service API description
     * @param string   $endpoint      Endpoint to connect to
     * @param JsonBody $jsonFormatter Optional JSON formatter to use
     */
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api, $endpoint, \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody $jsonFormatter = null)
    {
        parent::__construct($api, $endpoint);
        $this->contentType = \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody::getContentType($api);
        $this->jsonFormatter = $jsonFormatter ?: new \WPStack_Connect_Vendor\Aws\Api\Serializer\JsonBody($api);
    }
    protected function payload(\WPStack_Connect_Vendor\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $body = isset($value) ? (string) $this->jsonFormatter->build($member, $value) : "{}";
        $opts['headers']['Content-Type'] = $this->contentType;
        $opts['body'] = $body;
    }
}
