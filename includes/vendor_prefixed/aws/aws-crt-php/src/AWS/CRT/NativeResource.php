<?php

/**
 * Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0.
 */
namespace WPStack_Connect_Vendor\AWS\CRT;

use WPStack_Connect_Vendor\AWS\CRT\CRT as CRT;
/**
 * Base class for all native resources, tracks all outstanding resources
 * and provides basic leak checking
 */
abstract class NativeResource
{
    protected static $crt = null;
    protected static $resources = [];
    protected $native = null;
    protected function __construct()
    {
        if (\is_null(self::$crt)) {
            self::$crt = new \WPStack_Connect_Vendor\AWS\CRT\CRT();
        }
        self::$resources[\spl_object_hash($this)] = 1;
    }
    protected function acquire($handle)
    {
        return $this->native = $handle;
    }
    protected function release()
    {
        $native = $this->native;
        $this->native = null;
        return $native;
    }
    function __destruct()
    {
        // Should have been destroyed and released by derived resource
        \assert($this->native == null);
        unset(self::$resources[\spl_object_hash($this)]);
    }
}
