<?php

namespace WPStack_Connect_Vendor\Aws\Endpoint\UseDualstackEndpoint\Exception;

use WPStack_Connect_Vendor\Aws\HasMonitoringEventsTrait;
use WPStack_Connect_Vendor\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for useDualstackRegion
 */
class ConfigurationException extends \RuntimeException implements \WPStack_Connect_Vendor\Aws\MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
