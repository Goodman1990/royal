<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\SubcategoriesProductModelEntity;

class SubcategoriesProductModel extends SubcategoriesProductModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            'title'=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
            'number'=>array('validators' =>array('regex' => 'numbers'),'setLabel' => 'порядковый номер'),
            'visible'=>array('typeInput' => 'checkbox','filters' => array('int'),'validators' =>false,'setLabel' => 'видемость','labelClass'),
            'image'=>array('validators' =>false,'typeInput' => 'hidden','required' => true),
            'id'=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('int')),
            'id_categories_product'=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')));
    }


    public function getCategory(){

            $multijoin = array(
                array(
                    array('cp'=>'categories_product'),
                    'cp.id = subcategories_product.id_categories_product',
                    array(
                        '*'
                    )
                ),
            );
//        $this->setAttributes(array('id_course'=>$id_course, 'deleted'=>0));
//        $columns = array('id', 'title', 'number', 'id_course');

        $this->setCriteria(array(
            'columns' =>$this->getAttributes(),
            'multijoin'=>$multijoin,
            'where'=> array(),
//            'order'   => 'lesson.number ASC'
        ));
        var_dump($this->getSQLByCriteria());
        exit;
        return $this->findByCriteria();

    }

}