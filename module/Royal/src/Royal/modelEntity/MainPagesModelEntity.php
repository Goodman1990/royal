<?php 
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\modelEntity;


use ActiveRecord\ActiveRecordModel;

class MainPagesModelEntity extends ActiveRecordModel {

    

    public function rules()
    {
        return array(array(implode(',',$this->attributeNames()),'match','pattern'=>'/(.*)/'));
    }

    public function getTableName()
    {
        return "main_pages";
    }

    public function attributeNames()
    {
        return array(
            "id",
            "id_category_pages",
            "decription",
            "content",
        );
    }

} 