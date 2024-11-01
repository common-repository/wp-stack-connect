<?php

namespace WPStack_Connect_Vendor\Aws\Api;

/**
 * Represents a list shape.
 */
class ListShape extends \WPStack_Connect_Vendor\Aws\Api\Shape
{
    private $member;
    public function __construct(array $definition, \WPStack_Connect_Vendor\Aws\Api\ShapeMap $shapeMap)
    {
        $definition['type'] = 'list';
        parent::__construct($definition, $shapeMap);
    }
    /**
     * @return Shape
     * @throws \RuntimeException if no member is specified
     */
    public function getMember()
    {
        if (!$this->member) {
            if (!isset($this->definition['member'])) {
                throw new \RuntimeException('No member attribute specified');
            }
            $this->member = \WPStack_Connect_Vendor\Aws\Api\Shape::create($this->definition['member'], $this->shapeMap);
        }
        return $this->member;
    }
}
