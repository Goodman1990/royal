<?php 
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\modelEntity;


use ActiveRecord\ActiveRecordModel;

class ProductModelEntity extends ActiveRecordModel {

    

    public function rules()
    {
        return array(array(implode(',',$this->attributeNames()),'match','pattern'=>'/(.*)/'));
    }

    public function getTableName()
    {
        return "product";
    }

    public function attributeNames()
    {
        return array(
            "id",
"id_subcategories_product",
"id_categories_product",
"id_manufacturers",
"title",
"description",
"technical_description",
"price",
"addres_buy",
"video",
"date_create",
"file",
"image",
"main_image",

        );
    }

} 