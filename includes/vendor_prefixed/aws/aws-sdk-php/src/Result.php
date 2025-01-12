<?php

namespace WPStack_Connect_Vendor\Aws;

use WPStack_Connect_Vendor\JmesPath\Env as JmesPath;
/**
 * AWS result.
 */
class Result implements \WPStack_Connect_Vendor\Aws\ResultInterface, \WPStack_Connect_Vendor\Aws\MonitoringEventsInterface
{
    use HasDataTrait;
    use HasMonitoringEventsTrait;
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    public function hasKey($name)
    {
        return isset($this->data[$name]);
    }
    public function get($key)
    {
        return $this[$key];
    }
    public function search($expression)
    {
        return \WPStack_Connect_Vendor\JmesPath\Env::search($expression, $this->toArray());
    }
    public function __toString()
    {
        $jsonData = \json_encode($this->toArray(), \JSON_PRETTY_PRINT);
        return <<<EOT
Model Data
----------
Data can be retrieved from the model object using the get() method of the
model (e.g., `\$result->get(\$key)`) or "accessing the result like an
associative array (e.g. `\$result['key']`). You can also execute JMESPath
expressions on the result data using the search() method.

{$jsonData}

EOT;
    }
    /**
     * @deprecated
     */
    public function getPath($path)
    {
        return $this->search(\str_replace('/', '.', $path));
    }
}
