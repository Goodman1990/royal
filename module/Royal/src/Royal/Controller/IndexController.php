<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonRoyal for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Royal\Controller;

use Page\Page;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\Feature;
use Zend\Mvc\MvcEvent;
use Royal\Models;


class IndexController extends AbstractActionController
{
	public $adapter;
    public $id_page;
    public $Page;
    public $view;


    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach('dispatch', array($this, 'preDispatch'), 100);
        $events->attach('dispatch', array($this, 'postDispatch'), -100);
    }

    public function preDispatch (MvcEvent $e){
        $this->Page = new Page();
    }

    public function PostDispatch (MvcEvent $e){
        $this->layout()->setVariables(array('page'=>$this->Page));
    }

    private function parseParam($id_page){

       $pos =  stripos($id_page, '_', 0);
       return substr($id_page,0,$pos);

    }

    public function indexAction() {

        $this->id_page = $this->parseParam($this->params()->fromRoute('id_page', 0));
        if($this->id_page==0){

            $elementCategory =  array_shift(Models\CategoryPagesModel::model()->findAll());
            $this->id_page = $elementCategory['id'];

        }
        $this->Page->setActivePage(array('top'=>$this->id_page));
        $this->layout()->setVariables(array('page'=>$this->Page));

        return new ViewModel(array(
        ));
    }
}
