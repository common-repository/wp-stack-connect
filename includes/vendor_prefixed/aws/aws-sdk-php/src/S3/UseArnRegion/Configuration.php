<?php

namespace WPStack_Connect_Vendor\Aws\S3\UseArnRegion;

use WPStack_Connect_Vendor\Aws;
use WPStack_Connect_Vendor\Aws\S3\UseArnRegion\Exception\ConfigurationException;
class Configuration implements \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\ConfigurationInterface
{
    private $useArnRegion;
    public function __construct($useArnRegion)
    {
        $this->useArnRegion = \WPStack_Connect_Vendor\Aws\boolean_value($useArnRegion);
        if (\is_null($this->useArnRegion)) {
            throw new \WPStack_Connect_Vendor\Aws\S3\UseArnRegion\Exception\ConfigurationException("'use_arn_region' config option" . " must be a boolean value.");
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isUseArnRegion()
    {
        return $this->useArnRegion;
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['use_arn_region' => $this->isUseArnRegion()];
    }
}
