<?php

namespace WPStack_Connect_Vendor\Aws\Arn\S3;

use WPStack_Connect_Vendor\Aws\Arn\ArnInterface;
/**
 * @internal
 */
interface OutpostsArnInterface extends \WPStack_Connect_Vendor\Aws\Arn\ArnInterface
{
    public function getOutpostId();
}
