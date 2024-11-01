<?php

namespace WPStack_Connect_Vendor\Aws\Handler\GuzzleV6;

use Exception;
use WPStack_Connect_Vendor\GuzzleHttp\Exception\ConnectException;
use WPStack_Connect_Vendor\GuzzleHttp\Exception\RequestException;
use WPStack_Connect_Vendor\GuzzleHttp\Promise;
use WPStack_Connect_Vendor\GuzzleHttp\Client;
use WPStack_Connect_Vendor\GuzzleHttp\ClientInterface;
use WPStack_Connect_Vendor\GuzzleHttp\TransferStats;
use WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface as Psr7Request;
/**
 * A request handler that sends PSR-7-compatible requests with Guzzle 6.
 */
class GuzzleHandler
{
    /** @var ClientInterface */
    private $client;
    /**
     * @param ClientInterface $client
     */
    public function __construct(\WPStack_Connect_Vendor\GuzzleHttp\ClientInterface $client = null)
    {
        $this->client = $client ?: new \WPStack_Connect_Vendor\GuzzleHttp\Client();
    }
    /**
     * @param Psr7Request $request
     * @param array       $options
     *
     * @return Promise\Promise
     */
    public function __invoke(\WPStack_Connect_Vendor\Psr\Http\Message\RequestInterface $request, array $options = [])
    {
        $request = $request->withHeader('User-Agent', $request->getHeaderLine('User-Agent') . ' ' . \WPStack_Connect_Vendor\GuzzleHttp\default_user_agent());
        return $this->client->sendAsync($request, $this->parseOptions($options))->otherwise(static function ($e) {
            $error = ['exception' => $e, 'connection_error' => $e instanceof \WPStack_Connect_Vendor\GuzzleHttp\Exception\ConnectException, 'response' => null];
            if ($e instanceof \WPStack_Connect_Vendor\GuzzleHttp\Exception\RequestException && $e->getResponse()) {
                $error['response'] = $e->getResponse();
            }
            return new \WPStack_Connect_Vendor\GuzzleHttp\Promise\RejectedPromise($error);
        });
    }
    private function parseOptions(array $options)
    {
        if (isset($options['http_stats_receiver'])) {
            $fn = $options['http_stats_receiver'];
            unset($options['http_stats_receiver']);
            $prev = isset($options['on_stats']) ? $options['on_stats'] : null;
            $options['on_stats'] = static function (\WPStack_Connect_Vendor\GuzzleHttp\TransferStats $stats) use($fn, $prev) {
                if (\is_callable($prev)) {
                    $prev($stats);
                }
                $transferStats = ['total_time' => $stats->getTransferTime()];
                $transferStats += $stats->getHandlerStats();
                $fn($transferStats);
            };
        }
        return $options;
    }
}
