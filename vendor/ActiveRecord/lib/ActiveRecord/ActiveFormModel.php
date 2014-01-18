<?php
namespace ActiveRecord;



/**
 * ActiveRecordModel
 */
abstract class ActiveFormModel extends ActiveRecordModel
{
    /**
     * @var array $errors
     */


    /**
     * Processing form
     * @param \Royal\Form\formGenerate $form
     * @param \Zend\Stdlib\RequestInterface $request
     * @return bool|\Royal\Form\formGenerate
     */

    public function formProceed(\Royal\Form\formGenerate $form, \Zend\Stdlib\RequestInterface $request)
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
