<?php
namespace Training\Form;

use ActiveForm\ActiveFormModel;

class AddUserForm extends ActiveFormModel
{
   /**
     * Create statically model
     * @return ActiveRecordModel
     */
    public static function model($options = null, $className=__CLASS__)
    {
        return parent::model($options, $className);
    }

    public function rules()
    {
        return array();
    }

    public function attributeNames()
    {
       return array();
    }

    public function onSubmit()
    {
        return false;
    }
}
