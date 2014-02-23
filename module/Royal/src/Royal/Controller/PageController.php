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
use Royal\helpers\generalHelper;


class PageController extends AbstractActionController
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

        $this->id_page = $this->parseParam($this->params()->fromRoute('param1', 0));

//        var_dump($this->id_page);
//        exit;
        if($this->id_page==0){
            $elementCategory =  array_shift(Models\CategoryPagesModel::model()->findAll());
            $this->id_page = $elementCategory['id'];
        }
        $this->Page->setActivePage(array('top'=>$this->id_page));

        $MainPagesModel =  \Royal\Models\MainPagesModel::model();
        $dataPage = $MainPagesModel->findByAttributes(array('id_category_pages'=>$this->id_page));

        return new ViewModel(array(
            'dataPage'=>$dataPage
        ));
    }

    public function categoriesAction() {

        $this->id_page = $this->parseParam($this->params()->fromRoute('param1', 0));


        if($this->id_page==0){
            $elementCategory =   \Royal\Models\CategoryPagesModel::model(array('asArray'=>true))->findAll();
            $this->id_page = $elementCategory[0]['id'];
        }

        $SubcategoriesProductModel =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true));
        $SubcategoriesProductData = $SubcategoriesProductModel->findByAttributes(array(
            'id_categories_product'=> $this->id_page
        ));

        $this->Page->setActivePage(array('bottom'=>$this->id_page));
        $generalHelper = new generalHelper();

        return new ViewModel(array(
            'page'=>$this->Page,
            'SubcategoriesProductData'=>$SubcategoriesProductData,
            'generalHelper'=>$generalHelper,

        ));

    }
    public function manufacturersAction() {

        $this->id_page = $this->parseParam($this->params()->fromRoute('param1', 0));
        $SubcategoriesProductModel = \Royal\Models\SubcategoriesProductModel::model()->findByPk($this->id_page);

        if($this->id_page==0){
            $elementCategory =  array_shift( \Royal\Models\ManufacturersModel::model()->findAll());
            $this->id_page = $elementCategory['id'];
        }

        $ManufacturersModel =  \Royal\Models\ManufacturersModel::model(array('asArray'=>true));
        $manufacturersData = $ManufacturersModel->findByAttributes(array(
            'id_subcategories_product'=> $this->id_page
        ));

        $this->Page->setActivePage(array('bottom'=>$SubcategoriesProductModel->id_categories_product,'right'=>$SubcategoriesProductModel->id));
        $generalHelper = new generalHelper();
        
//        echo'<pre>';
//        var_dump($manufacturersData);
//        exit;
        return new ViewModel(array(
            'page'=>$this->Page,
            'manufacturersData'=>$manufacturersData,
            'generalHelper'=>$generalHelper

        ));

    }
    public function productAction() {

        $this->id_page = $this->parseParam($this->params()->fromRoute('param1', 0));


        $ManufacturersModel =  \Royal\Models\ManufacturersModel::model()->findByPk($this->id_page);



        $ProductModel =  \Royal\Models\ProductModel::model(array('asArray'=>true));
        $productData = $ProductModel->findByAttributes(array(
            'id_manufacturers'=> $this->id_page
        ));

        if($this->id_page==0){
            $elementCategory =  array_shift( \Royal\Models\ManufacturersModel::model()->findAll());
            $this->id_page = $elementCategory['id'];
        }

        $SubcategoriesProductModel = \Royal\Models\SubcategoriesProductModel::model()->findByAttributes(array(
            'id'=> $ManufacturersModel->id_subcategories_product
        ));

        $this->Page->setActivePage(
            array(
                'bottom'=>$SubcategoriesProductModel->id_categories_product,
                'right'=>$ManufacturersModel->id_subcategories_product,
                'right_manufacturers'=>$ManufacturersModel->id.'_manufacturers'
            ));
        $generalHelper = new generalHelper();

        return new ViewModel(array(
            'page'=>$this->Page,
            'generalHelper'=>$generalHelper,
            'productData'=>$productData

        ));

    }



    public function productDescriptionAction() {

        $this->id_page = $this->parseParam($this->params()->fromRoute('param1', 0));
        $ProductModel =  \Royal\Models\ProductModel::model()->findByPk($this->id_page);

        $productData = $ProductModel->findByAttributes(array(
            'id_manufacturers'=> $this->id_page
        ));
        $SubcategoriesProductModel = \Royal\Models\SubcategoriesProductModel::model()->findByAttributes(array(
            'id'=> $ProductModel->id_subcategories_product
        ));
        $ManufacturersModel = \Royal\Models\ManufacturersModel::model()->findByAttributes(array(
            'id'=> $ProductModel->id_manufacturers
        ));

        $this->Page->setActivePage(
            array(
                'bottom'=>$SubcategoriesProductModel->id_categories_product,
                'right'=>$ManufacturersModel->id_subcategories_product,
                'right_manufacturers'=>$ManufacturersModel->id.'_manufacturers'
            ));
        $generalHelper = new generalHelper();

        return new ViewModel(array(
            'page'=>$this->Page,
            'generalHelper'=>$generalHelper,
            'productData'=>$productData

        ));

    }


}
