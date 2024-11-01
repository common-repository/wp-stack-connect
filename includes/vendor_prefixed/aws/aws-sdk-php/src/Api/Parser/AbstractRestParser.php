<?php

namespace WPStack_Connect_Vendor\Aws\Api\Parser;

use WPStack_Connect_Vendor\Aws\Api\DateTimeResult;
use WPStack_Connect_Vendor\Aws\Api\Shape;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
use WPStack_Connect_Vendor\Aws\Result;
use WPStack_Connect_Vendor\Aws\CommandInterface;
use WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface;
/**
 * @internal
 */
abstract class AbstractRestParser extends \WPStack_Connect_Vendor\Aws\Api\Parser\AbstractParser
{
    use PayloadParserTrait;
    /**
     * Parses a payload from a response.
     *
     * @param ResponseInterface $response Response to parse.
     * @param StructureShape    $member   Member to parse
     * @param array             $result   Result value
     *
     * @return mixed
     */
    protected abstract function payload(\WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, \WPStack_Connect_Vendor\Aws\Api\StructureShape $member, array &$result);
    public function __invoke(\WPStack_Connect_Vendor\Aws\CommandInterface $command, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response)
    {
        $output = $this->api->getOperation($command->getName())->getOutput();
        $result = [];
        if ($payload = $output['payload']) {
            $this->extractPayload($payload, $output, $response, $result);
        }
        foreach ($output->getMembers() as $name => $member) {
            switch ($member['location']) {
                case 'header':
                    $this->extractHeader($name, $member, $response, $result);
                    break;
                case 'headers':
                    $this->extractHeaders($name, $member, $response, $result);
                    break;
                case 'statusCode':
                    $this->extractStatus($name, $response, $result);
                    break;
            }
        }
        if (!$payload && $response->getBody()->getSize() > 0 && \count($output->getMembers()) > 0) {
            // if no payload was found, then parse the contents of the body
            $this->payload($response, $output, $result);
        }
        return new \WPStack_Connect_Vendor\Aws\Result($result);
    }
    private function extractPayload($payload, \WPStack_Connect_Vendor\Aws\Api\StructureShape $output, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, array &$result)
    {
        $member = $output->getMember($payload);
        if (!empty($member['eventstream'])) {
            $result[$payload] = new \WPStack_Connect_Vendor\Aws\Api\Parser\EventParsingIterator($response->getBody(), $member, $this);
        } else {
            if ($member instanceof \WPStack_Connect_Vendor\Aws\Api\StructureShape) {
                // Structure members parse top-level data into a specific key.
                $result[$payload] = [];
                $this->payload($response, $member, $result[$payload]);
            } else {
                // Streaming data is just the stream from the response body.
                $result[$payload] = $response->getBody();
            }
        }
    }
    /**
     * Extract a single header from the response into the result.
     */
    private function extractHeader($name, \WPStack_Connect_Vendor\Aws\Api\Shape $shape, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, &$result)
    {
        $value = $response->getHeaderLine($shape['locationName'] ?: $name);
        switch ($shape->getType()) {
            case 'float':
            case 'double':
                $value = (float) $value;
                break;
            case 'long':
                $value = (int) $value;
                break;
            case 'boolean':
                $value = \filter_var($value, \FILTER_VALIDATE_BOOLEAN);
                break;
            case 'blob':
                $value = \base64_decode($value);
                break;
            case 'timestamp':
                try {
                    $value = \WPStack_Connect_Vendor\Aws\Api\DateTimeResult::fromTimestamp($value, !empty($shape['timestampFormat']) ? $shape['timestampFormat'] : null);
                    break;
                } catch (\Exception $e) {
                    // If the value cannot be parsed, then do not add it to the
                    // output structure.
                    return;
                }
            case 'string':
                try {
                    if ($shape['jsonvalue']) {
                        $value = $this->parseJson(\base64_decode($value), $response);
                    }
                    // If value is not set, do not add to output structure.
                    if (!isset($value)) {
                        return;
                    }
                    break;
                } catch (\Exception $e) {
                    //If the value cannot be parsed, then do not add it to the
                    //output structure.
                    return;
                }
        }
        $result[$name] = $value;
    }
    /**
     * Extract a map of headers with an optional prefix from the response.
     */
    private function extractHeaders($name, \WPStack_Connect_Vendor\Aws\Api\Shape $shape, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, &$result)
    {
        // Check if the headers are prefixed by a location name
        $result[$name] = [];
        $prefix = $shape['locationName'];
        $prefixLen = $prefix !== null ? \strlen($prefix) : 0;
        foreach ($response->getHeaders() as $k => $values) {
            if (!$prefixLen) {
                $result[$name][$k] = \implode(', ', $values);
            } elseif (\stripos($k, $prefix) === 0) {
                $result[$name][\substr($k, $prefixLen)] = \implode(', ', $values);
            }
        }
    }
    /**
     * Places the status code of the response into the result array.
     */
    private function extractStatus($name, \WPStack_Connect_Vendor\Psr\Http\Message\ResponseInterface $response, array &$result)
    {
        $result[$name] = (int) $response->getStatusCode();
    }
}
