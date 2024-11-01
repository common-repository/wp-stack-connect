<?php

namespace WPStack_Connect_Vendor\Aws\Api\Parser;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Aws\ResultInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal
 */
abstract class AbstractParser
{
    /** @var \Aws\Api\Service Representation of the service API*/
    protected $api;
    /** @var callable */
    protected $parser;
    /**
     * @param Service $api Service description.
     */
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api)
    {
        $this->api = $api;
    }
    /**
     * @param CommandInterface  $command  Command that was executed.
     * @param ResponseInterface $response Response that was received.
     *
     * @return ResultInterface
     */
    public abstract function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response);
    public abstract function parseMemberFromStream(\WPStack_Connect_Vendor\Psr\Http\Message\StreamInterface $stream, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member, $response);
}
