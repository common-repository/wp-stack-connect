<?php

namespace WPStack_Connect_Vendor\JmesPath;

/**
 * Returns data from the input array that matches a JMESPath expression.
 *
 * @param string $expression Expression to search.
 * @param mixed $data Data to search.
 *
 * @return mixed
 */
if (!\function_exists(__NAMESPACE__ . '\\search')) {
    function search($expression, $data)
    {
        return \WPStack_Connect_Vendor\JmesPath\Env::search($expression, $data);
    }
}
