<?php
namespace Royal\helpers;

use Zend\Session\Container;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;
use Zend\Http\Request;



class AuthHelper
{
    protected  $conteiner;
    protected  $sessionManager;
    protected  $request;


    public function __construct($sm) {

        $this->conteiner = new Container('admin');
        $this->sessionManager = $sm;
    }

    public function isLogin(){


        $this->sessionManager->isValid();

        if($this->conteiner->offsetGet('auth')){
            return $this->isValid();
        }
            return false;
    }

    private function isValid(){

        $HttpUserAgent = new HttpUserAgent($this->conteiner->offsetGet('agent'));
        $RemoteAddr = new RemoteAddr($this->conteiner->offsetGet('HTTP_USER_AGENT'));

        if($HttpUserAgent->isValid() && $RemoteAddr->isValid()){

            return $this->conteiner->offsetGet('id');

        }else{
            $this->sessionManager->destroy();
            return false;
        }

    }


    public function auth($data){
        $UserModel = \Royal\Models\UsersModel::model();
        $data['password'] = $UserModel::hashPassword($data['password']);
        $UserModel = $UserModel::model()->findByAttributes(array($data));

        if( $UserModel != array() ) {

            foreach($UserModel->getAttributes() as $key=>$value){
                $this->conteiner->offsetSet($key,$value);
            }
            $this->conteiner->ofsetSet('auth',true);
            $this->conteiner->ofsetSet('ip',$_SERVER['REMOTE_ADDR']);
            $this->conteiner->ofsetSet('agent',$_SERVER['HTTP_USER_AGENT']);

            return $UserModel;

        }

        return false;

    }

    public function  exitSession(){

        $this->sessionManager->destroy();

    }

    public function getSession(){

       return $this->conteiner;

    }

    public function getManager(){

        return $this->sessionManager;

    }


}
