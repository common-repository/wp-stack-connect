<?php

namespace WPStack_Connect_Vendor\Aws\Endpoint\UseFipsEndpoint;

use WPStack_Connect_Vendor\Aws;
use WPStack_Connect_Vendor\Aws\Endpoint\UseFipsEndpoint\Exception\ConfigurationException;
class Configuration implements \WPStack_Connect_Vendor\Aws\Endpoint\UseFipsEndpoint\ConfigurationInterface
{
    private $useFipsEndpoint;
    public function __construct($useFipsEndpoint)
    {
        $this->useFipsEndpoint = \WPStack_Connect_Vendor\Aws\boolean_value($useFipsEndpoint);
        if (\is_null($this->useFipsEndpoint)) {
            throw new \WPStack_Connect_Vendor\Aws\Endpoint\UseFipsEndpoint\Exception\ConfigurationException("'use_fips_endpoint' config option" . " must be a boolean value.");
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isUseFipsEndpoint()
    {
        return $this->useFipsEndpoint;
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['use_fips_endpoint' => $this->isUseFipsEndpoint()];
    }
}
