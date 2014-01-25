<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\CategoriesProductModelEntity;

class CategoriesProductModel extends CategoriesProductModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            'title'=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
            'number'=>array('filters' => array('int'),'validators' =>array('regex' => 'numbers'),'setLabel' => 'порядковый номер'),
            'visible'=>array('typeInput' => 'checkbox','filters' => array('int'),'validators' =>false,'setLabel' => 'видемость'),
            'id'=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int'))
        );
    }

}