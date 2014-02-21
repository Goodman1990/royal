<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Royal\Models;

use Royal\modelEntity\MainPagesModelEntity;

class MainPagesModel extends MainPagesModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array(
            "id"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "id_category_pages"=>array('typeInput' => 'hidden','validators' =>false,'filters' => array('trim','int')),
            "decription"=>array('typeInput' => 'hidden','validators' =>false,'filters' =>false),
            "content"=>array('required' => true,'typeInput'=>'textarea','validators' => false,'filters' => false, 'setLabel' => 'Содержимое'),
        );
    }

}