<?php

/**
 * Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0.
 */
namespace WPStack_Connect_Vendor\AWS\CRT\HTTP;

class Response extends \WPStack_Connect_Vendor\AWS\CRT\HTTP\Message
{
    private $status_code;
    public function __construct($method, $path, $query, $headers, $status_code)
    {
        parent::__construct($method, $path, $query, $headers);
        $this->status_code = $status_code;
    }
    public static function marshall($response)
    {
        return parent::marshall($response);
    }
    public static function unmarshall($buf)
    {
        return parent::_unmarshall($buf, \WPStack_Connect_Vendor\AWS\CRT\HTTP\Response::class);
    }
    public function status_code()
    {
        return $this->status_code;
    }
}
