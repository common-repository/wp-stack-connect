<?php

namespace WPStack_Connect_Vendor\Aws\Endpoint\UseFipsEndpoint;

interface ConfigurationInterface
{
    /**
     * Returns whether or not to use a FIPS endpoint
     *
     * @return bool
     */
    public function isUseFipsEndpoint();
    /**
     * Returns the configuration as an associative array
     *
     * @return array
     */
    public function toArray();
}
