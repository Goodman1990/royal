<?php
namespace Page;
 
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Page\Form\CustomValidator;
use Zend\View\Model\ViewModel;

class Page
{
    public $classes;
    public $css;
    public $js;
    public $active_page;
    public $tab;
    public $activeTabTitle;
    public $controller;
    public $action;
    public $paramRoute;
    public $attributes;
    public $renderMenu = true;


    public function __set($name, $value) {


            $this->$name = $value;


    }

    public function setClasses($classes){
        $this->classes = $classes;
    }

    public function setCss($css){
        $this->css = $css;
    }

    public function setJs($js){
        $this->js = $js;
    }

    public function setActivePage($page){
        $this->activePage=$page;
    }
    public function setRenderMenu($flag){
        $this->renderMenu =$flag;
    }

    public function addTab($tabs,$activeTab,$format=false) {
        if($format){
         $tabs = $this->formatTabsData($tabs,$activeTab);
        }
        $this->tab = $tabs;

    }

    public function formatTabsData($data,$activeTab){

        for($i=0;$i<count($data);$i++){
            if($activeTab ==$data[$i]['id']){
                $data[$i]['active'] = true;
                $this->activeTabTitle = $data[$i]['title'];
            }
            if(!empty($this->paramRoute)){
                $data[$i]['link'] = '/'.$this->controller.'/'.$this->action.'/'.$this->paramRoute[0].'/'.$data[$i]['id'];
            }else{
                $data[$i]['link'] = '/'.$this->controller.'/'.$this->action.'/'.$data[$i]['id'];
            }

        }
        return $data;
    }

    public  function setData($data) {

        foreach($data as $key=>$value){
            $this->$key = $value;
        }

    }


}
