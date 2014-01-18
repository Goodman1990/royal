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
        $this->active_page=$page;
    }


}
