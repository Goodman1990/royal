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
            "title"=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название продукта'),
            "description"=>array('required' => true,'typeInput'=>'textarea','validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'Описание'),
            "count"=>array('validators' => array('regex' => 'numbers',), 'setLabel' => 'количество'),
            "date_create"=>array('validators' => false,'filters'=>false),
            "file"=>array('validators' =>false,'typeInput' => 'hidden','required' => true),
            "image"=>array('validators' =>false,'typeInput' => 'hidden','required' => true),
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_subcategories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_categories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_manufacturers"=>array('required' => true,'typeInput' => 'select','validators' =>false,'filters' => array('trim','int'),'setLabel' => 'Выбрать категорию'),
        );
    }

}