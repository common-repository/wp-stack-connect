<?php

namespace WPStack_Connect_Vendor\Aws\EndpointV2\Rule;

use WPStack_Connect_Vendor\Aws\EndpointV2\Ruleset\RulesetStandardLibrary;
class TreeRule extends \WPStack_Connect_Vendor\Aws\EndpointV2\Rule\AbstractRule
{
    /** @var array */
    private $rules;
    public function __construct(array $definition)
    {
        parent::__construct($definition);
        $this->rules = $this->createRules($definition['rules']);
    }
    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }
    /**
     * If a tree rule's conditions evaluate successfully, iterate over its
     * subordinate rules and return a result if there is one. If any of the
     * subsequent rules are trees, the function will recurse until it reaches
     * an error or an endpoint rule
     *
     * @return mixed
     */
    public function evaluate(array $inputParameters, \WPStack_Connect_Vendor\Aws\EndpointV2\Ruleset\RulesetStandardLibrary $standardLibrary)
    {
        if ($this->evaluateConditions($inputParameters, $standardLibrary)) {
            foreach ($this->rules as $rule) {
                $inputParametersCopy = $inputParameters;
                $evaluation = $rule->evaluate($inputParametersCopy, $standardLibrary);
                if ($evaluation !== \false) {
                    return $evaluation;
                }
            }
        }
        return \false;
    }
    private function createRules(array $rules)
    {
        $rulesList = [];
        foreach ($rules as $rule) {
            $ruleType = \WPStack_Connect_Vendor\Aws\EndpointV2\Rule\RuleCreator::create($rule['type'], $rule);
            $rulesList[] = $ruleType;
        }
        return $rulesList;
    }
}
