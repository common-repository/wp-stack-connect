<?php

namespace WPStack_Connect_Vendor\Aws\Api\Parser;

use WPStack_Connect_Vendor\Aws\Api\DateTimeResult;
use WPStack_Connect_Vendor\Aws\Api\ListShape;
use WPStack_Connect_Vendor\Aws\Api\MapShape;
use WPStack_Connect_Vendor\Aws\Api\Parser\Exception\ParserException;
use WPStack_Connect_Vendor\Aws\Api\Shape;
use WPStack_Connect_Vendor\Aws\Api\StructureShape;
/**
 * @internal Implements standard XML parsing for REST-XML and Query protocols.
 */
class XmlParser
{
    public function parse(\WPStack_Connect_Vendor\Aws\Api\StructureShape $shape, \SimpleXMLElement $value)
    {
        return $this->dispatch($shape, $value);
    }
    private function dispatch($shape, \SimpleXMLElement $value)
    {
        static $methods = ['structure' => 'parse_structure', 'list' => 'parse_list', 'map' => 'parse_map', 'blob' => 'parse_blob', 'boolean' => 'parse_boolean', 'integer' => 'parse_integer', 'float' => 'parse_float', 'double' => 'parse_float', 'timestamp' => 'parse_timestamp'];
        $type = $shape['type'];
        if (isset($methods[$type])) {
            return $this->{$methods[$type]}($shape, $value);
        }
        return (string) $value;
    }
    private function parse_structure(\WPStack_Connect_Vendor\Aws\Api\StructureShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        foreach ($shape->getMembers() as $name => $member) {
            // Extract the name of the XML node
            $node = $this->memberKey($member, $name);
            if (isset($value->{$node})) {
                $target[$name] = $this->dispatch($member, $value->{$node});
            } else {
                $memberShape = $shape->getMember($name);
                if (!empty($memberShape['xmlAttribute'])) {
                    $target[$name] = $this->parse_xml_attribute($shape, $memberShape, $value);
                }
            }
        }
        if (isset($shape['union']) && $shape['union'] && empty($target)) {
            foreach ($value as $key => $val) {
                $name = $val->children()->getName();
                $target['Unknown'][$name] = $val->{$name};
            }
        }
        return $target;
    }
    private function memberKey(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $name)
    {
        if (null !== $shape['locationName']) {
            return $shape['locationName'];
        }
        if ($shape instanceof \WPStack_Connect_Vendor\Aws\Api\ListShape && $shape['flattened']) {
            return $shape->getMember()['locationName'] ?: $name;
        }
        return $name;
    }
    private function parse_list(\WPStack_Connect_Vendor\Aws\Api\ListShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        $member = $shape->getMember();
        if (!$shape['flattened']) {
            $value = $value->{$member['locationName'] ?: 'member'};
        }
        foreach ($value as $v) {
            $target[] = $this->dispatch($member, $v);
        }
        return $target;
    }
    private function parse_map(\WPStack_Connect_Vendor\Aws\Api\MapShape $shape, \SimpleXMLElement $value)
    {
        $target = [];
        if (!$shape['flattened']) {
            $value = $value->entry;
        }
        $mapKey = $shape->getKey();
        $mapValue = $shape->getValue();
        $keyName = $shape->getKey()['locationName'] ?: 'key';
        $valueName = $shape->getValue()['locationName'] ?: 'value';
        foreach ($value as $node) {
            $key = $this->dispatch($mapKey, $node->{$keyName});
            $value = $this->dispatch($mapValue, $node->{$valueName});
            $target[$key] = $value;
        }
        return $target;
    }
    private function parse_blob(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        return \base64_decode((string) $value);
    }
    private function parse_float(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        return (float) (string) $value;
    }
    private function parse_integer(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        return (int) (string) $value;
    }
    private function parse_boolean(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        return $value == 'true';
    }
    private function parse_timestamp(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, $value)
    {
        if (\is_string($value) || \is_int($value) || \is_object($value) && \method_exists($value, '__toString')) {
            return \WPStack_Connect_Vendor\Aws\Api\DateTimeResult::fromTimestamp((string) $value, !empty($shape['timestampFormat']) ? $shape['timestampFormat'] : null);
        }
        throw new \WPStack_Connect_Vendor\Aws\Api\Parser\Exception\ParserException('Invalid timestamp value passed to XmlParser::parse_timestamp');
    }
    private function parse_xml_attribute(\WPStack_Connect_Vendor\Aws\Api\Shape $shape, \WPStack_Connect_Vendor\Aws\Api\Shape $memberShape, $value)
    {
        $namespace = $shape['xmlNamespace']['uri'] ? $shape['xmlNamespace']['uri'] : '';
        $prefix = $shape['xmlNamespace']['prefix'] ? $shape['xmlNamespace']['prefix'] : '';
        if (!empty($prefix)) {
            $prefix .= ':';
        }
        $key = \str_replace($prefix, '', $memberShape['locationName']);
        $attributes = $value->attributes($namespace);
        return isset($attributes[$key]) ? (string) $attributes[$key] : null;
    }
}
