<?php

namespace WPStack_Connect_Vendor\Aws\EndpointV2\Rule;

use WPStack_Connect_Vendor\Aws\EndpointV2\Ruleset\RulesetStandardLibrary;
use WPStack_Connect_Vendor\Aws\Exception\UnresolvedEndpointException;
class ErrorRule extends \WPStack_Connect_Vendor\Aws\EndpointV2\Rule\AbstractRule
{
    /** @var array */
    private $error;
    public function __construct($definition)
    {
        parent::__construct($definition);
        $this->error = $definition['error'];
    }
    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * If an error rule's conditions are met, raise an
     * UnresolvedEndpointError containing the fully resolved error string.
     *
     * @return null
     * @throws UnresolvedEndpointException
     */
    public function evaluate(array $inputParameters, \WPStack_Connect_Vendor\Aws\EndpointV2\Ruleset\RulesetStandardLibrary $standardLibrary)
    {
        if ($this->evaluateConditions($inputParameters, $standardLibrary)) {
            $message = $standardLibrary->resolveValue($this->error, $inputParameters);
            throw new \WPStack_Connect_Vendor\Aws\Exception\UnresolvedEndpointException($message);
        }
        return \false;
    }
}
