<?php

namespace WPStack_Connect_Vendor\GuzzleHttp;

use WPStack_Connect_Vendor\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \WPStack_Connect_Vendor\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(\WPStack_Connect_Vendor\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Message::bodySummary($message) : \WPStack_Connect_Vendor\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
