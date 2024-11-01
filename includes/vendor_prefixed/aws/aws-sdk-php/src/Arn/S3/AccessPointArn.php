<?php

namespace WPStack_Connect_Vendor\Aws\Arn\S3;

use WPStack_Connect_Vendor\Aws\Arn\AccessPointArn as BaseAccessPointArn;
use WPStack_Connect_Vendor\Aws\Arn\AccessPointArnInterface;
use WPStack_Connect_Vendor\Aws\Arn\ArnInterface;
use WPStack_Connect_Vendor\Aws\Arn\Exception\InvalidArnException;
/**
 * @internal
 */
class AccessPointArn extends \WPStack_Connect_Vendor\Aws\Arn\AccessPointArn implements \WPStack_Connect_Vendor\Aws\Arn\AccessPointArnInterface
{
    /**
     * Validation specific to AccessPointArn
     *
     * @param array $data
     */
    public static function validate(array $data)
    {
        parent::validate($data);
        if ($data['service'] !== 's3') {
            throw new \WPStack_Connect_Vendor\Aws\Arn\Exception\InvalidArnException("The 3rd component of an S3 access" . " point ARN represents the region and must be 's3'.");
        }
    }
}
