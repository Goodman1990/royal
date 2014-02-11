<?php 
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\modelEntity;


use ActiveRecord\ActiveRecordModel;

class SubcategoriesProductModelEntity extends ActiveRecordModel {

    

    public function rules()
    {
        return array(array(implode(',',$this->attributeNames()),'match','pattern'=>'/(.*)/'));
    }

    public function getTableName()
    {
        return "subcategories_product";
    }

    public function attributeNames()
    {
        return array(
            "id",
"id_categories_product",
"title",
"visible",
"image",
"number",

        );
    }

} 