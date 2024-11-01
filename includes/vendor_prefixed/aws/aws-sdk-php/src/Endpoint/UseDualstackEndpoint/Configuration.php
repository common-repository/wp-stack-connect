<?php

namespace WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint;

use WPStack_Connect_Vendor\Aws;
use WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException;
class Configuration implements \WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint\ConfigurationInterface
{
    private $useDualstackEndpoint;
    public function __construct($useDualstackEndpoint, $region)
    {
        $this->useDualstackEndpoint = \WPStack_Connect_Vendor\Aws\boolean_value($useDualstackEndpoint);
        if (\is_null($this->useDualstackEndpoint)) {
            throw new \WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException("'use_dual_stack_endpoint' config option" . " must be a boolean value.");
        }
        if ($this->useDualstackEndpoint == \true && (\strpos($region, "iso-") !== \false || \strpos($region, "-iso") !== \false)) {
            throw new \WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception\ConfigurationException("Dual-stack is not supported in ISO regions");
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isUseDualstackEndpoint()
    {
        return $this->useDualstackEndpoint;
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['use_dual_stack_endpoint' => $this->isUseDualstackEndpoint()];
    }
}
