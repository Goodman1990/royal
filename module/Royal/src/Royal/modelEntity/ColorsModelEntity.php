<?php 
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\modelEntity;


use ActiveRecord\ActiveRecordModel;

class ColorsModelEntity extends ActiveRecordModel {

    

    public function rules()
    {
        return array(array(implode(',',$this->attributeNames()),'match','pattern'=>'/(.*)/'));
    }

    public function getTableName()
    {
        return "colors";
    }

    public function attributeNames()
    {
        return array(
            "id",
"title",
"group",
"typeImage",

        );
    }

} 