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
            $data[$i]['link'] = '/admin/subcategories/'.$data[$i]['id'];
        }
        return $data;
    }


}
