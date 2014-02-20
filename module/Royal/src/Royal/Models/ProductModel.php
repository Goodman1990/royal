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
            "description"=>array('required' => true,'typeInput'=>'textarea','validators' => false, 'setLabel' => 'Описание'),
            "technical_description"=>array('required' => true,'typeInput'=>'textarea','validators' => false, 'setLabel' => 'Техническое описание'),
            "video"=>array('typeInput'=>'textarea','validators' => false, 'class'=>'video','setLabel' => 'Добавить ссылки на видео'),
            "addres_buy"=>array('typeInput'=>'textarea','validators' => array('regex' => 'numbers_letters',),'class'=>'addres_buy','setLabel' => 'Где купить'),
            "price"=>array('required' => true,'validators' => array('regex' => 'numbers',), 'setLabel' => 'Цена'),
            "file"=>array('validators' =>false,'typeInput' => 'hidden'),
            "image"=>array('validators' =>false,'typeInput' => 'hidden'),
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_subcategories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_categories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_manufacturers"=>array('required' => true,'typeInput' => 'select','validators' =>false,'filters' => array('trim','int'),'setLabel' => 'Выбрать категорию'),
        );
    }


    public function getProduct($options) {

        $this->setCriteria(array(
            'where'=>array("title LIKE '".mysql_real_escape_string($options->search)."'")));

        return $this->findByCriteria();

    }

}