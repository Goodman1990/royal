<?php
namespace ActiveRecord;

use Helper;

/**
 * ActiveRecordModel
 */
abstract class ActiveRecordModel extends ActiveRecord
{
    /**
     * @var array $errors
     */
    protected $errors = array();

    /**
     * @var array $_models
     */
    private static $_models=array();            // class name => model

    /**
     * Rules is abstract function
     */
    abstract public function rules();

    /**
     * List of attributes
     */
    abstract public function attributeNames();

    /**
     * @return array of default options
     */
    public function defaults()
    {
        return array();
    }

    /**
     * Get model errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set model errors as array
     * @param array $errors
     * @return void
     */
    public function setErrors($errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * Is model has any errors
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->getErrors());
    }

    /**
     * Add error to model
     * @param string $attribute
     * @param string $errorMsg
     * @return void
     */
    public function addError($attribute, $errorMsg)
    {
        $this->errors[] = array($attribute => $errorMsg);
    }

    /**
     * Validate function
     * @return bolean if model is valid
     */
    public function validate()
    {
        return true;
    }

    /**
     * Format data before validation
     * @return boolean
     */
    public function beforeValidate()
    {
        return true;
    }

    /**
     * Save model
     * @return boolean if model has been saved
     */
    public function save()
    {

            $this->write();


//        return !$this->hasErrors();
    }

    /**
     * Returns the static model of the specified AR class.
     * The model returned is a static instance of the AR class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * EVERY derived AR class must override this method as follows,
     * <pre>
     * public static function model($className=__CLASS__)
     * {
     *     return parent::model($className);
     * }
     * </pre>
     *
     * @param string $className active record class name.
     * @return ActiveRecordModel active record model instance.
     */
    public static function model($options = null, $className = __CLASS__)
    {
        if (isset(self::$_models[$className])) {
            $model = self::$_models[$className];
            $model->setCriteria(array(), false);
            $clearAttributes = array_fill_keys($model->attributeNames(), null);
            $defaultAttributes = (null !== $model->defaults()) ? array_merge($clearAttributes, $model->defaults()) : $clearAttributes;
        } else {
            $model = self::$_models[$className] = new $className(null);
            $defaultAttributes = (null !== $model->defaults()) ? $model->defaults() : array();
        }
        $model->clearInstance();
        $model->attributes = $defaultAttributes;
        if (null !== $options) {
            foreach ($options as $key => $value) {
                $model->$key = $value;
            }
        }
        return $model;
    }

    /**
     * @return array of default settings
     */
    public function getARMDefaultSettings()
    {
        return array(
            'errors' => array(),
        );
    }

    /**
     * Clear AR model
     * @return void
     */
    public function clearInstance()
    {
        foreach (array_merge($this->getARDefaultSettings(), $this->getARMDefaultSettings()) as $attribute => $value) {
            $this->$attribute = $value;
        }
    }

    /**
     * Get translated errors
     * @param Translator $translator
     * @return array of model error
     */
    public function getTranslatedErrors(\Zend\Mvc\I18n\Translator $translator)
    {
        return array_map(function($error)use($translator){
                $key = key($error);
                return array($key => sprintf($translator->translate($error[$key]), $key));
        }, $this->getErrors());
    }

    /**
     * Get array copy for form processing
     * @return array of attributes
     */
    public function getArrayCopy()
    {
        return $this->attributes;
    }

    /**
     * Processing form
     * @param \Training\Form\formGenerate $form
     * @param \Zend\Stdlib\RequestInterface $request
     * @return bool|\Training\Form\formGenerate
     */
    public function formProceed(\Training\Form\formGenerate $form, \Zend\Stdlib\RequestInterface $request)
    {
        if($request->isPost()){
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->setAttributes(array_merge($this->attributes, $form->getData()));
                return $this->save();
            }else{
                return $form;
            }
        }else{
            $form->bind($this);
            return $form;
        }

        return true;
    }
}
