<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\GroupProductModelEntity;

class GroupProductModel extends GroupProductModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_subcategories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_manufacturers"=>array('typeInput' => 'select','validators' =>false,'filters' => array('trim','int'),'setLabel' => 'производители'),
            "id_categories_product"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "title"=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название групы'),
            "image"=>array('validators' => false,'typeInput' => 'hidden'),
            'number'=>array('validators' =>array('regex' => 'numbers'),'setLabel' => 'порядковый номер'),
        );
    }

}