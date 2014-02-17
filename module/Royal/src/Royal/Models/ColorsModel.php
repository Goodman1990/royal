<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\ColorsModelEntity;

class ColorsModel extends ColorsModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            'title'=>array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
            "group"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "typeImage"=>array('typeInput' => 'checkbox','validators' =>false,'setLabel' => 'видемость'),
        );
    }

}