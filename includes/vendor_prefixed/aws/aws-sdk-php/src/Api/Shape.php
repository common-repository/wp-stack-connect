<?php

namespace WPStack_Connect_Vendor\Aws\Api;

/**
 * Base class representing a modeled shape.
 */
class Shape extends \WPStack_Connect_Vendor\Aws\Api\AbstractModel
{
    /**
     * Get a concrete shape for the given definition.
     *
     * @param array    $definition
     * @param ShapeMap $shapeMap
     *
     * @return mixed
     * @throws \RuntimeException if the type is invalid
     */
    public static function create(array $definition, \WPStack_Connect_Vendor\Aws\Api\ShapeMap $shapeMap)
    {
        static $map = ['structure' => \WPStack_Connect_Vendor\Aws\Api\StructureShape::class, 'map' => \WPStack_Connect_Vendor\Aws\Api\MapShape::class, 'list' => \WPStack_Connect_Vendor\Aws\Api\ListShape::class, 'timestamp' => \WPStack_Connect_Vendor\Aws\Api\TimestampShape::class, 'integer' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'double' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'float' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'long' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'string' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'byte' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'character' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'blob' => \WPStack_Connect_Vendor\Aws\Api\Shape::class, 'boolean' => \WPStack_Connect_Vendor\Aws\Api\Shape::class];
        if (isset($definition['shape'])) {
            return $shapeMap->resolve($definition);
        }
        if (!isset($map[$definition['type']])) {
            throw new \RuntimeException('Invalid type: ' . \print_r($definition, \true));
        }
        $type = $map[$definition['type']];
        return new $type($definition, $shapeMap);
    }
    /**
     * Get the type of the shape
     *
     * @return string
     */
    public function getType()
    {
        return $this->definition['type'];
    }
    /**
     * Get the name of the shape
     *
     * @return string
     */
    public function getName()
    {
        return $this->definition['name'];
    }
    /**
     * Get a context param definition.
     */
    public function getContextParam()
    {
        return $this->contextParam;
    }
}
