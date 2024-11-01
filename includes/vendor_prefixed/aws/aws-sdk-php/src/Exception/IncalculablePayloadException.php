<?php

namespace WPStack_Connect_Vendor\Aws\Exception;

use WPStack_Connect_Vendor\Aws\HasMonitoringEventsTrait;
use WPStack_Connect_Vendor\Aws\MonitoringEventsInterface;
class IncalculablePayloadException extends \RuntimeException implements \WPStack_Connect_Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
