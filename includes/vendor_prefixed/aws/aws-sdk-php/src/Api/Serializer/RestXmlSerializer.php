<?php

namespace WPStack_Connect_Vendor\Aws\Api\Serializer;

use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Aws\Api\Service;
/**
 * @internal
 */
class RestXmlSerializer extends \WPStack_Connect_Vendor\Aws\Api\Serializer\RestSerializer
{
    /** @var XmlBody */
    private $xmlBody;
    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     * @param XmlBody $xmlBody  Optional XML formatter to use
     */
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api, $endpoint, \WPStack_Connect_Vendor\Aws\Api\Serializer\XmlBody $xmlBody = null)
    {
        parent::__construct($api, $endpoint);
        $this->xmlBody = $xmlBody ?: new \WPStack_Connect_Vendor\Aws\Api\Serializer\XmlBody($api);
    }
    protected function payload(\WPStack_Connect_Vendor\Aws\Api\StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = 'application/xml';
        $opts['body'] = $this->getXmlBody($member, $value);
    }
    /**
     * @param StructureShape $member
     * @param array $value
     * @return string
     */
    private function getXmlBody(\WPStack_Connect_Vendor\Aws\Api\StructureShape $member, array $value)
    {
        $xmlBody = (string) $this->xmlBody->build($member, $value);
        $xmlBody = \str_replace("'", "&apos;", $xmlBody);
        $xmlBody = \str_replace('\\r', "&#13;", $xmlBody);
        $xmlBody = \str_replace('\\n', "&#10;", $xmlBody);
        return $xmlBody;
    }
}
