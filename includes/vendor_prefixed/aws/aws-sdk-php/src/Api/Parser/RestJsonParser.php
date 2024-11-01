<?php

namespace WPStack_Connect_Vendor\Aws\Api\Parser;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements REST-JSON parsing (e.g., Glacier, Elastic Transcoder)
 */
class RestJsonParser extends \WPStack_Connect_Vendor\Aws\Api\Parser\AbstractRestParser
{
    use PayloadParserTrait;
    /**
     * @param Service    $api    Service description
     * @param JsonParser $parser JSON body builder
     */
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api, \WPStack_Connect_Vendor\Aws\Api\Parser\JsonParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \WPStack_Connect_Vendor\Aws\Api\Parser\JsonParser();
    }
    protected function payload(\WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member, array &$result)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            $result += $this->parser->parse($member, $jsonBody);
        }
    }
    public function parseMemberFromStream(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $stream, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member, $response)
    {
        $jsonBody = $this->parseJson($stream, $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
        return [];
    }
}
