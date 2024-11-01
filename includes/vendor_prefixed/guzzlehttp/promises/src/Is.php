<?php

namespace WPStack_Connect_Vendor\GuzzleHttp\Promise;

final class Is
{
    /**
     * Returns true if a promise is pending.
     *
     * @return bool
     */
    public static function pending(\WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled or rejected.
     *
     * @return bool
     */
    public static function settled(\WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() !== \WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface::PENDING;
    }
    /**
     * Returns true if a promise is fulfilled.
     *
     * @return bool
     */
    public static function fulfilled(\WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface::FULFILLED;
    }
    /**
     * Returns true if a promise is rejected.
     *
     * @return bool
     */
    public static function rejected(\WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface $promise)
    {
        return $promise->getState() === \WPStack_Connect_Vendor\GuzzleHttp\Promise\PromiseInterface::REJECTED;
    }
}
