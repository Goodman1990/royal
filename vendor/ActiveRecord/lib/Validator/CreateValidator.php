<?php
namespace Validator;

use \Zend\Validator\EmailAddress;

class CreateValidator
{
    protected static $instance;
    protected static $validators;

    private $_errors = array();
    private $_model;

    private function __construct(){ /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
    private function __clone()    { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
    private function __wakeup()   { /* ... @return Singleton */ }  // Защищаем от создания через unserialize

    /**
     * @return CreateValidator is singleton
     */
    public static function getInstance()
    {
        if ( !isset(self::$instance) ) {
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }

    /**
     * @return array of errors
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Clear $this->_errors
     */
    public function clearErrors()
    {
        $this->_errors = array();
    }

    /**
     * @return boolean if CreateValidator has any errors
     */
    public function hasErrors()
    {
        return (boolean) count($this->getErrors());
    }

    /**
     * Add error
     * @param string $attribute name of model attribute
     * @param string $message error message
     * @return void
     */
    public function addError($attribute, $message)
    {
        $this->_errors[] = array($attribute => $message);
    }

    /**
     * Validate
     * @param array $rule
     * @return CreateValidator
     */
    public function validate($model, $rule)
    {
        $this->clearErrors();
        $this->_model = $model;

        $attributes = explode(',', $rule[0]);
        $validatorName = $rule[1];

        $validatorParams = array_slice($rule, 2);

        foreach ($attributes as $attribute) {
            $this->createValidator($attribute, $validatorName, $validatorParams);
        }

        return $this;
    }

    /**
     * Create current validator by validator tag
     * @param string $attribute name of model attribute
     * @param string $validatorName validator tag
     * @param string $validatorParams validator params
     * @return boolean
     */
    public function createValidator($attribute, $validatorName, $validatorParams)
    {
        $attributeValue = $this->_model->$attribute;
        switch ($validatorName) {
            case 'match':
                $this->MatchValidator($attribute, $attributeValue, $validatorParams['pattern']);
                return !$this->hasErrors();
            case 'required':
                $this->RequiredValidator($attribute, $attributeValue);
                return !$this->hasErrors();
            case 'email':
                $validator = new EmailAddress(array('isValid' => $attributeValue,'useDomainCheck'=>false));
                if (count($validator->getMessages())) {
                    $this->addError($attribute, 'Invalid email address!');
                }
                return !$this->hasErrors();
        }
    }

    /**
     * MatchValidator
     * validate model by preg_match
     * @param string $attribute name of model attribute
     * @param string $attributeValue value of model attribute
     * @param string $pattern
     * @return boolean
     */
    public function MatchValidator($attribute, $attributeValue, $pattern)
    {
        if (!preg_match($pattern, $attributeValue)) {
            $this->addError($attribute, "Field %s is invalid!");
        }

        return !count($this->getErrors());
    }

    /**
     * RequiredValidator
     * validate model by preg_match
     * @param string $attribute name of model attribute
     * @param string $attributeValue value of model attribute
     * @return boolean
     */
    public function RequiredValidator($attribute, $attributeValue)
    {
        $pattern = '/^[\s\t]*$/';
        if (preg_match($pattern, $attributeValue)) {
            $this->addError($attribute, "Field %s is required!");
        }

        return !count($this->getErrors());
    }
}
