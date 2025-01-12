<?php

/**
 * Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0.
 */
namespace WPStack_Connect_Vendor\AWS\CRT\Auth;

use WPStack_Connect_Vendor\AWS\CRT\IO\InputStream;
use WPStack_Connect_Vendor\AWS\CRT\NativeResource as NativeResource;
class Signable extends \WPStack_Connect_Vendor\AWS\CRT\NativeResource
{
    public static function fromHttpRequest($http_message)
    {
        return new \WPStack_Connect_Vendor\AWS\CRT\Auth\Signable(function () use($http_message) {
            return self::$crt->signable_new_from_http_request($http_message->native);
        });
    }
    public static function fromChunk($chunk_stream, $previous_signature = "")
    {
        if (!$chunk_stream instanceof \WPStack_Connect_Vendor\AWS\CRT\IO\InputStream) {
            $chunk_stream = new \WPStack_Connect_Vendor\AWS\CRT\IO\InputStream($chunk_stream);
        }
        return new \WPStack_Connect_Vendor\AWS\CRT\Auth\Signable(function () use($chunk_stream, $previous_signature) {
            return self::$crt->signable_new_from_chunk($chunk_stream->native, $previous_signature);
        });
    }
    public static function fromCanonicalRequest($canonical_request)
    {
        return new \WPStack_Connect_Vendor\AWS\CRT\Auth\Signable(function () use($canonical_request) {
            return self::$crt->signable_new_from_canonical_request($canonical_request);
        });
    }
    protected function __construct($ctor)
    {
        parent::__construct();
        $this->acquire($ctor());
    }
    function __destruct()
    {
        self::$crt->signable_release($this->release());
        parent::__destruct();
    }
}
