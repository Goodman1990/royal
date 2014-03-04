<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\ManufacturersModelEntity;

class ManufacturersModel extends ManufacturersModelEntity {

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
            'id_subcategories_product'=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')));
    }


    public function getManufacturers($arrayId){

        $this->setCriteria(array(
            'where'=>array('id_subcategories_product in ('.implode(',',$arrayId).')')

        ));

        return $this->findByCriteria();



    }

}