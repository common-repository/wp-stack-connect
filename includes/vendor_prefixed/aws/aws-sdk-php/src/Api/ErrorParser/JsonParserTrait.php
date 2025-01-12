<?php

namespace WPStack_Connect_Vendor\Aws\Api\ErrorParser;

use WPStack_Connect_Vendor\Aws\Api\Parser\PayloadParserTrait;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
/**
 * Provides basic JSON error parsing functionality.
 */
trait JsonParserTrait
{
    use PayloadParserTrait;
    private function genericHandler(\WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $code = (string) $response->getStatusCode();
        if ($this->api && !\is_null($this->api->getMetadata('awsQueryCompatible')) && $response->getHeaderLine('x-amzn-query-error')) {
            $queryError = $response->getHeaderLine('x-amzn-query-error');
            $parts = \explode(';', $queryError);
            if (isset($parts) && \count($parts) == 2 && $parts[0] && $parts[1]) {
                $error_code = $parts[0];
                $error_type = $parts[1];
            }
        }
        if (!isset($error_type)) {
            $error_type = $code[0] == '4' ? 'client' : 'server';
        }
        return ['request_id' => (string) $response->getHeaderLine('x-amzn-requestid'), 'code' => isset($error_code) ? $error_code : null, 'message' => null, 'type' => $error_type, 'parsed' => $this->parseJson($response->getBody(), $response)];
    }
    protected function payload(\WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member)
    {
        $jsonBody = $this->parseJson($response->getBody(), $response);
        if ($jsonBody) {
            return $this->parser->parse($member, $jsonBody);
        }
    }
}
