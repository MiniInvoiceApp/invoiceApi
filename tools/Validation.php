<?php

class Validation
{
    private $errorMessages;

    public function __construct()
    {
        $this->errorMessages = [];
    }

    /**
     * Validate data based on the rules
     *
     * @param $request
     * @param $rules
     */
    public function validate($request, $rules)
    {
        foreach ($rules as $key => $rule) {
            //get rules for each key
            $allRules = explode("|", $rule);

            //check if requested field is set into request
            if (array_key_exists($key, $request)) {
                if (!empty($allRules)) {
                    foreach ($allRules as $r) {
                        $ruleValue = null;

                        //for rules that include rule values we have to split them into rule name (in order to initialize
                        //the function) and to the value of the rule
                        if (strstr($r, "=")) {
                            $explodeRule = explode("=", $r);
                            $r = $explodeRule[0];
                            $ruleValue = $explodeRule[1];
                        }

                        //call validation rule function
                        $this->$r($key, $request[$key], $ruleValue);
                    }
                }
            } else {
                //if a parameter is not set on the request body but it is required
                //we add the following error on errorMessages array
                if (in_array("required", $allRules)) {
                    $this->addErrorMessage("$key is required");
                }
            }
        }
    }

    /**
     * Check if value is not null or has zero length
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function required($paramName, $paramValue, $ruleValue)
    {
        if (!is_array($paramValue)) {
            if (strlen(trim($paramValue)) == 0 || $paramValue == null) {
                $this->addErrorMessage("$paramName cannot be null or empty");
            }
        }
    }

    /**
     * Check if string length is greater than the required value
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function maxLength($paramName, $paramValue, $ruleValue)
    {
        if (strlen($paramValue) > $ruleValue) {
            $this->addErrorMessage("$paramName cannot be greater than $ruleValue characters long");
        }
    }

    /**
     * Check if string length is less than the required value
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function minLength($paramName, $paramValue, $ruleValue)
    {
        if (strlen($paramValue) < $ruleValue) {
            $this->addErrorMessage("$paramName cannot be less than $ruleValue characters long");
        }
    }

    /**
     * Check if the value is numeric
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function numeric($paramName, $paramValue, $ruleValue)
    {
        if (!is_numeric($paramValue)) {
            $this->addErrorMessage("$paramName must be numeric");
        }
    }

    /**
     * Check if the value is float
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function float($paramName, $paramValue, $ruleValue)
    {
        if (!is_float($paramValue)) {
            $this->addErrorMessage("$paramName must be float");
        }
    }

    /**
     * Check if the value exists in requested values
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function in($paramName, $paramValue, $ruleValue)
    {
        $rules = explode(",", $ruleValue);

        if (!in_array($paramValue, $rules)) {
            $this->addErrorMessage("$paramName must accept only $ruleValue values");
        }
    }

    /**
     * Check if the value is array
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function isArray($paramName, $paramValue, $ruleValue)
    {
        if (!is_array($paramValue)) {
            $this->addErrorMessage("$paramName must be array");
        }
    }

    /**
     * Check if the value is array
     *
     * @param $paramName
     * @param $paramValue
     * @param $ruleValue
     */
    private function notEmptyArray($paramName, $paramValue, $ruleValue)
    {
        if (empty($paramValue)) {
            $this->addErrorMessage("$paramName cannot be an empty array");
        }
    }

    /**
     * Add error messages on errorMessages array
     *
     * @param $message
     */
    private function addErrorMessage($message)
    {
        $this->errorMessages[] = $message;
    }

    /**
     * Check if errorMessages array is empty or not
     * If it is not empty means that some validation errors occurred
     *
     * @return bool
     */
    public function hasErrors()
    {
        if (!empty($this->errorMessages)) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Return error messages
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}
