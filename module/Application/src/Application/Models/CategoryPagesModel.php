<?php /**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Application\Models;

use Application\modelEntity\CategoryPagesModelEntity;

class CategoryPagesModel extends CategoryPagesModelEntity {

    public static function model($options = null,$className=__CLASS__)
    {
        return parent::model($options, $className);
    }


    public function rules()
    {
        return array();
    }

}