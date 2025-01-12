<?php

/**
 * Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0.
 */
namespace WPStack_Connect_Vendor\AWS\CRT\Auth;

use WPStack_Connect_Vendor\AWS\CRT\NativeResource as NativeResource;
/**
 * Base class for credentials providers
 */
abstract class CredentialsProvider extends \WPStack_Connect_Vendor\AWS\CRT\NativeResource
{
    function __construct(array $options = [])
    {
        parent::__construct();
    }
    function __destruct()
    {
        self::$crt->credentials_provider_release($this->release());
        parent::__destruct();
    }
}
