<?php

namespace WPStack_Connect_Vendor\Aws\Api\Parser;

use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Result;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements JSON-RPC parsing (e.g., DynamoDB)
 */
class JsonRpcParser extends \WPStack_Connect_Vendor\Aws\Api\Parser\AbstractParser
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
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $operation = $this->api->getOperation($command->getName());
        $result = null === $operation['output'] ? null : $this->parseMemberFromStream($response->getBody(), $operation->getOutput(), $response);
        return new \WPStack_Connect_Vendor\Aws\Result($result ?: []);
    }
    public function parseMemberFromStream(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $stream, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member, $response)
    {
        return $this->parser->parse($member, $this->parseJson($stream, $response));
    }
}
