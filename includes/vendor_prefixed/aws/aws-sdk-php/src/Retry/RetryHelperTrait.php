<?php

namespace WPStack_Connect_Vendor\Aws\Retry;

use WPStack_Connect_Vendor\Aws\Exception\AwsException;
use WPStack_Connect_Vendor\Aws\ResultInterface;
trait RetryHelperTrait
{
    private function addRetryHeader($request, $retries, $delayBy)
    {
        return $request->withHeader('aws-sdk-retry', "{$retries}/{$delayBy}");
    }
    private function updateStats($retries, $delay, array &$stats)
    {
        if (!isset($stats['total_retry_delay'])) {
            $stats['total_retry_delay'] = 0;
        }
        $stats['total_retry_delay'] += $delay;
        $stats['retries_attempted'] = $retries;
    }
    private function updateHttpStats($value, array &$stats)
    {
        if (empty($stats['http'])) {
            $stats['http'] = [];
        }
        if ($value instanceof \WPStack_Connect_Vendor\Aws\Exception\AwsException) {
            $resultStats = $value->getTransferInfo();
            $stats['http'][] = $resultStats;
        } elseif ($value instanceof \WPStack_Connect_Vendor\Aws\ResultInterface) {
            $resultStats = isset($value['@metadata']['transferStats']['http'][0]) ? $value['@metadata']['transferStats']['http'][0] : [];
            $stats['http'][] = $resultStats;
        }
    }
    private function bindStatsToReturn($return, array $stats)
    {
        if ($return instanceof \WPStack_Connect_Vendor\Aws\ResultInterface) {
            if (!isset($return['@metadata'])) {
                $return['@metadata'] = [];
            }
            $return['@metadata']['transferStats'] = $stats;
        } elseif ($return instanceof \WPStack_Connect_Vendor\Aws\Exception\AwsException) {
            $return->setTransferInfo($stats);
        }
        return $return;
    }
}
