<?php

namespace WPStack_Connect_Vendor\GuzzleHttp;

use WPStack_Connect_Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(\WPStack_Connect_Vendor\Psr\Http\Message\MessageInterface $message) : ?string;
}
