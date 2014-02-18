<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\ProductModelEntity;

class ProductModel extends ProductModelEntity {

    public static function model($options = null,$className=__CLASS__) {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            "title"=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'Название продукта'),
            "description"=>array('typeInput'=>'textarea','validators' => false, 'setLabel' => 'Описание'),
            "video"=>array('typeInput'=>'textarea','validators' => array('regex' => 'numbers_letters',), 'class'=>'video','setLabel' => 'Добавить ссылки на видео'),
            "addres_buy"=>array('typeInput'=>'textarea','validators' => array('regex' => 'numbers_letters',),'class'=>'addres_buy','setLabel' => 'Где купить'),
            "count"=>array('validators' => array('regex' => 'numbers',), 'setLabel' => 'Количество'),
            "price"=>array('validators' => array('regex' => 'numbers',), 'setLabel' => 'Цена'),
            "file"=>array('validators' =>false,'typeInput' => 'hidden'),
            "image"=>array('validators' =>false,'typeInput' => 'hidden','required' => true),
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_subcategories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_categories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_manufacturers"=>array('required' => true,'typeInput' => 'select','validators' =>false,'filters' => array('trim','int'),'setLabel' => 'Выбрать категорию'),


//            "id",
//            "id_subcategories_product",
//            "id_categories_product",
//            "id_manufacturers",
//            "title",
//            "description",
//            "price",
//            "addres_buy",
//            "video",
//            "count",
//            "date_create",
//            "file",
//            "image",



        );
    }

}