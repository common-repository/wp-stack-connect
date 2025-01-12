<?php

namespace WPStack_Connect_Vendor\Aws\Retry;

use WPStack_Connect_Vendor\Aws\Retry\Exception\ConfigurationException;
class Configuration implements \WPStack_Connect_Vendor\Aws\Retry\ConfigurationInterface
{
    private $mode;
    private $maxAttempts;
    private $validModes = ['legacy', 'standard', 'adaptive'];
    public function __construct($mode = 'legacy', $maxAttempts = 3)
    {
        $mode = \strtolower($mode);
        if (!\in_array($mode, $this->validModes)) {
            throw new \WPStack_Connect_Vendor\Aws\Retry\Exception\ConfigurationException("'{$mode}' is not a valid mode." . " The mode has to be 'legacy', 'standard', or 'adaptive'.");
        }
        if (!\is_numeric($maxAttempts) || \intval($maxAttempts) != $maxAttempts || $maxAttempts < 1) {
            throw new \WPStack_Connect_Vendor\Aws\Retry\Exception\ConfigurationException("The 'maxAttempts' parameter has" . " to be an integer >= 1.");
        }
        $this->mode = $mode;
        $this->maxAttempts = \intval($maxAttempts);
    }
    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }
    /**
     * {@inheritdoc}
     */
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return ['mode' => $this->getMode(), 'max_attempts' => $this->getMaxAttempts()];
    }
}
