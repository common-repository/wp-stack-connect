<?php

namespace WPStack_Connect_Vendor\Aws\Arn\S3;

use WPStack_Connect_Vendor\Aws\Arn\Arn;
use WPStack_Connect_Vendor\Aws\Arn\ResourceTypeAndIdTrait;
/**
 * This class represents an S3 multi-region bucket ARN, which is in the
 * following format:
 *
 * @internal
 */
class MultiRegionAccessPointArn extends \WPStack_Connect_Vendor\Aws\Arn\S3\AccessPointArn
{
    use ResourceTypeAndIdTrait;
    /**
     * Parses a string into an associative array of components that represent
     * a MultiRegionArn
     *
     * @param $string
     * @return array
     */
    public static function parse($string)
    {
        return parent::parse($string);
    }
    /**
     *
     * @param array $data
     */
    public static function validate(array $data)
    {
        \WPStack_Connect_Vendor\Aws\Arn\Arn::validate($data);
    }
}
