<?php 
/**
 * Created by PhpStorm.
 * User: goodman
 * Date: 14.11.13
 * Time: 15:24
 */

namespace Application\modelEntity;


use ActiveRecord\ActiveRecordModel;

class UsersModelEntity extends ActiveRecordModel {

    

    public function rules()
    {
        return array(array(implode(',',$this->attributeNames()),'match','pattern'=>'/(.*)/'));
    }

    public function getTableName()
    {
        return "users";
    }

    public function attributeNames()
    {
        return array(
            "id",
"id_prev",
"email",
"username",
"surname",
"image",
"phone",
"isq",
"skype",
"password",
"status",
"deleted",
"registration_date",
"authorized_date",
"send_email",
"role",

        );
    }

} 