<?php

namespace WPStack_Connect_Vendor\Aws\Api\Serializer;

use WPStack_Connect_Vendor\Aws\Api\Service;
use WPStack_Connect_Vendor\Aws\Api\Shape;
use WPStack_Connect_Vendor\Aws\Api\TimestampShape;
use WPStack_Connect_Vendor\Aws\Exception\InvalidJsonException;
/**
 * Formats the JSON body of a JSON-REST or JSON-RPC operation.
 * @internal
 */
class JsonBody
{
    private $api;
    public function __construct(\WPStack_Connect_Vendor\Aws\Api\Service $api)
    {
        $this->api = $api;
    }
    /**
     * Gets the JSON Content-Type header for a service API
     *
     * @param Service $service
     *
     * @return string
     */
    public static function getContentType(\WPStack_Connect_Vendor\Aws\Api\Service $service)
    {
        if ($service->getMetadata('protocol') === 'rest-json') {
            return 'application/json';
        }
        $jsonVersion = $service->getMetadata('jsonVersion');
        if (empty($jsonVersion)) {
            throw new \InvalidArgumentException('invalid json');
        } else {
            return 'application/x-amz-json-' . @\number_format($service->getMetadata('jsonVersion'), 1);
        }
    }
    /**
     * Builds the JSON body based on an array of arguments.
     *
     * @param Shape $shape Operation being constructed
     * @param array $args  Associative array of arguments
     *
     * @return string
     */
    public function build(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, array $args)
    {
        $result = \json_encode($this->format($shape, $args));
        return $result == '[]' ? '{}' : $result;
    }
    private function format(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        switch ($shape['type']) {
            case 'structure':
                $data = [];
                if (isset($shape['document']) && $shape['document']) {
                    return $value;
                }
                foreach ($value as $k => $v) {
                    if ($v !== null && $shape->hasMember($k)) {
                        $valueShape = $shape->getMember($k);
                        $data[$valueShape['locationName'] ?: $k] = $this->format($valueShape, $v);
                    }
                }
                if (empty($data)) {
                    return new \stdClass();
                }
                return $data;
            case 'list':
                $items = $shape->getMember();
                foreach ($value as $k => $v) {
                    $value[$k] = $this->format($items, $v);
                }
                return $value;
            case 'map':
                if (empty($value)) {
                    return new \stdClass();
                }
                $values = $shape->getValue();
                foreach ($value as $k => $v) {
                    $value[$k] = $this->format($values, $v);
                }
                return $value;
            case 'blob':
                return \base64_encode($value);
            case 'timestamp':
                $timestampFormat = !empty($shape['timestampFormat']) ? $shape['timestampFormat'] : 'unixTimestamp';
                return \WPStack_Connect_Vendor\Aws\Api\TimestampShape::format($value, $timestampFormat);
            default:
                return $value;
        }
    }
}