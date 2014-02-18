<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonRoyal for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Royal\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\Feature;
use Royal\Form\formGenerate;
use Page\Page;
use Royal\helpers\generalHelper;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request;
use Royal\helpers\imageHelper;


class adminController extends AbstractActionController
{
    public $adapter;
    public $request;
    public $validData;
    public $Page;
    public $action;
    public $controller;


    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach('dispatch', array($this, 'preDispatch'), 100);
    }
    public function preDispatch (MvcEvent $e){

//        $this->action = $this->getAction();
//        $this->controller = $this->getController();

        $this->request = $this->getRequest();
        $this->Page = new Page();
        $route = $this->getParamsCustom();

        $this->Page->controller = $route['__CONTROLLER__'];
        $this->Page->action = $route['action'];


        $this->layout('layout/layoutAdmin');

        $this->layout()->setVariables(array('page'=>$this->Page));


    }

    private function getParamsCustom(){

        return  $route = $this->getEvent()->getRouteMatch()->getParams();
    }

    private function getRouteParam() {
        $routeParam = explode('/',$this->getRequest()->getUri()->toString());
        for($i=5;$i<count($routeParam);$i++){
            $this->Page->paramRoute[] =  $routeParam[$i];
        }
    }

    public function editCategoryAction()
    {
        $id_page = $this->params()->fromRoute('param1', 0);
        $this->getRouteParam();

        if($id_page=='page'){

            $model = \Royal\Models\CategoryPagesModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.2'
            )));

        }else if($id_page=='product'){

            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.1'
            )));
            $model = \Royal\Models\CategoriesProductModel::model();

        }

        $categoryData = $model->findAllOrder('number DESK ');//->addOrder('DESK number')->customExecute();

        $formEdit = new formGenerate('editCategory', 'category');
        $formEdit->setMultiFormEdit($model->rules(), $categoryData);
        $formAdd = new formGenerate('addCategory', 'category', $model);

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();

            if (isset($Post['edit'])) {
                $oldData = $formEdit->dataForSetForm;
                $formEdit->setData($Post);

                if ($formEdit->isValid()) {

                    $this->validData = $formEdit->getData();
                    for ($i = 0; $i < $formEdit->countInput; $i++) {
                        if($id_page == 'product'){
                            $generalHelper  = new generalHelper();
                            rename (SITE_DIR.$oldData['title_'.$i] ,SITE_DIR.'categories/'.$this->validData['image_' . $i]);
//
//                            rename(TMP_DIR.$this->validData['image_' . $i],SITE_DIR.'/product/'.$generalHelper->transliteration($manufacturerTitle['title']).'/'.$this->validData['image_' . $i]);
                        }
                        $model::model()
                            ->setAttributes(array(
                                'id' => $this->validData['id_' . $i],
                                'title' => $this->validData['title_' . $i],
                                'number'=>$this->validData['number_' . $i],
                                'visible'=>$this->validData['visible_' . $i],
                            ))->save();
                        $k = $this->validData['number_'.$i]-1;
                        $validData['id_'.$k] = $this->validData['id_'.$i];
                        $validData['title_'.$k] = $this->validData['title_'.$i];
                        $validData['number_'.$k] = $this->validData['number_'.$i];
                        $validData['visible_'.$k] = $this->validData['visible_'.$i];
                        $formEdit->setData($validData);

                    }
                }

            } else {
//                print_r($Post);
//                exit;
                $formAdd->setData($Post);

                if ($formAdd->isValid()) {

                    $this->validData = $formAdd->getData();

                    $id = $model::model()
                        ->setAttributes(array(
                            'title' => $this->validData['title'],
                            'number'=>$this->validData['number'],
                            'visible'=>$this->validData['visible'],
                        ))->save();
                    $formEdit->addInputForm($Post, $id);
                    $formEdit->CustomSetData();
                    $formAdd->clearElements();

                } else {

                    $formEdit->CustomSetData();

                }
            }
        } else {

            $formEdit->CustomSetData();

        }

        return new ViewModel(array(
            'formEdit' => $formEdit,
            'formAdd' => $formAdd,
            'categoryPageData' => $categoryData
        ));
    }

    public function editSubcategoriesAction() {


        $page = $this->params()->fromRoute('param1', 0);
        $this->getRouteParam();

        if($page=='subcategories'){

            $categoryData = \Royal\Models\CategoriesProductModel::model()->findAllOrder('number DESK ');
            $model = \Royal\Models\SubcategoriesProductModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.3'
            )));
            $rowTable = 'id_categories_product';

        }else{

            $categoryData = \Royal\Models\SubcategoriesProductModel::model()->findAllOrder('number DESK ');
            $model = \Royal\Models\ManufacturersModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.4'
            )));
            $rowTable = 'id_subcategories_product';

        }

        $id_page= $this->params()->fromRoute('param2', 0);

        if($id_page==0){
            $id_page = $categoryData[0]['id'];
        }


        $this->Page->addTab($categoryData,$id_page,true);

        $subcategoriesData = $model::model(array('asArray'=>true))
            ->findByAttributes(array($rowTable=>$id_page));

        $formEdit = new formGenerate('editSubCategory', 'category');
        $formEdit->setMultiFormEdit($model->rules(), $subcategoriesData);
        $formAdd = new formGenerate('addSubCategory', 'category', $model);

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();

            if (isset($Post['edit'])) {
                $oldData = $formEdit->dataForSetForm;
                $formEdit->setData($Post);
                if ($formEdit->isValid()) {

                    $this->validData = $formEdit->getData();

                    for ($i = 0; $i < $formEdit->countInput; $i++) {

                        unlink(SITE_DIR.'categories/'.$oldData['image_'.$i]);
                        rename(TMP_DIR.$this->validData['image_' . $i],SITE_DIR.'/categories/'.$this->validData['image_' . $i]);

                        $model::model()
                            ->setAttributes(array(
                                'id' => $this->validData['id_' . $i],
                                $rowTable => $this->validData[$rowTable.'_' . $i],
                                'title' => $this->validData['title_' . $i],
                                'number'=>$this->validData['number_' . $i],
                                'visible'=>$this->validData['visible_' . $i],
                                'image'=>$this->validData['image_' . $i]
                            ))->save();

                    }

                    $subcategoriesData = $model::model(array('asArray'=>true))
                        ->findByAttributes(array($rowTable=>$id_page));
                    $formEdit->setDataForSet($subcategoriesData);
                }

            } else {

                $formAdd->setData($Post);

                if ($formAdd->isValid()) {
                    $this->validData = $formAdd->getData();
                    unset($this->validData['id']);
//                    $generalHelper  = new generalHelper();
//                    mkdir(SITE_DIR.'categories/'.$generalHelper->transliteration($this->validData['title'].'/'), 0766, true);
                    rename(TMP_DIR.$this->validData['image'],SITE_DIR.'categories/'.$this->validData['image']);
                    $id = $model::model()
                        ->setAttributes($this->validData)->save();
                    $formEdit->addInputForm($Post, $id);
                    $formEdit->CustomSetData();
                    $formAdd->clearElements();

                } else {

                    $formEdit->CustomSetData();

                }
            }
        } else {

            $formEdit->CustomSetData();

        }

        return new ViewModel(array(
            'formEdit' => $formEdit,
            'formAdd' => $formAdd,
            'categoryPageData' => $categoryData,
            'id_page'=>$id_page,
            'tableRow'=>$rowTable,
            'subcategoriesData'=>$subcategoriesData
        ));


    }

    public function addProductAction() {

        $id_subcategories_product = $this->params()->fromRoute('param1', 0);
        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'2',
            'sub'=>'2.1'
        )));
        $subcategoriesData = \Royal\Models\SubcategoriesProductModel::model()->findAllOrder('number DESK ');
        $this->Page->addTab($subcategoriesData,$id_subcategories_product,true);
        $ManufacturersModel = \Royal\Models\ManufacturersModel::model(array('asArray'=>true));
        $formAddProduct= new formGenerate('addProduct', 'category addProduct');
        $formAddColor= new formGenerate('addColor', 'category addProduct color');
        $ProductModel = \Royal\Models\ProductModel::model(array('asArray'=>true));
        $rules =  $ProductModel->rules();

        if($id_subcategories_product==0){
            $id_subcategories_product = $subcategoriesData[0]['id'];
        }

       $categories =    array_pop(\Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByPk($id_subcategories_product));

        $manufacturers = $ManufacturersModel->findByAttributes(array(
            'id_subcategories_product'=>$id_subcategories_product
        ));

        $rules['id_manufacturers']['selectInfo'] = $manufacturers;

        $formAddProduct->setDataForm($rules);

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();
            $formAddProduct->setData($Post);

            if($formAddProduct->isValid()){

                $this->validData= $formAddProduct->getData();
                unset($this->validData['id']);

                $manufacturerTitle =  array_pop($ManufacturersModel->findByPk($this->validData['id_manufacturers']));
                $generalHelper  = new generalHelper();

                if(!file_exists(SITE_DIR.'product/'.$generalHelper->transliteration($manufacturerTitle['title']).'/')){
                    mkdir(SITE_DIR.'product/'.$generalHelper->transliteration($manufacturerTitle['title']).'/', 0777, false);
                }

                $image =explode(',',$this->validData['image']);
                for($i = 0;$i<count($image);$i++){
                    rename(TMP_DIR.$image[$i],SITE_DIR.'/product/'.$generalHelper->transliteration($manufacturerTitle['title']).'/'.$image[$i]);
                }
               \Royal\Models\ProductModel::model()->setAttributes($this->validData)->save();
            }
        }
        return new ViewModel(array(
            'formAdd'=>$formAddProduct,
            'id_subcategories_product'=>$id_subcategories_product,
            'categories_product_id'=>$categories['id']


        ));
    }

    public function cropAction() {

        $dataImage = $this->getRequest()->getPost()->toArray();
        $imageHelper  = new imageHelper(TMP_DIR.$dataImage['imageName']);
        $imageHelper->resize($dataImage['width'],$dataImage['height']);
        $imageHelper->cut($dataImage['x1'],$dataImage['y1'],$dataImage['w'],$dataImage['h']);
        $imageHelper->save();
        echo $dataImage['imageName'];
        exit;

    }

    public function uploadImageAction() {

        $this->Page = new Page();
        $this->layout()->setVariables(array('page'=>$this->Page));
        $request =new Request();
        if($request->isPost()){
            $httpadapter = new \Zend\File\Transfer\Adapter\Http();
            $filesize  = new \Zend\Validator\File\Size(array('min' => 1 ));
            $ext =   new \Zend\Validator\File\Extension(array('png', 'jpg','jpeg'));
            $httpadapter->setValidators(array($filesize,$ext));

            if($httpadapter->isValid()) {

                $httpadapter->setDestination(TMP_DIR);

                if($httpadapter->receive()) {

                    $filterRaname = new \Zend\Filter\File\Rename(array(
                        "randomize" => true,
                    ));
                    echo json_encode(basename(($filterRaname->filter($httpadapter->getFileName()))));


                }else{


                    exit;
                }
            }

            exit;
        }

        exit;
    }


    public function uploadPdfAction() {

        $this->Page = new Page();
        $this->layout()->setVariables(array('page'=>$this->Page));
        $request =new Request();
        if($request->isPost()){

            $httpadapter = new \Zend\File\Transfer\Adapter\Http();
            $filesize  = new \Zend\Validator\File\Size(array('min' => 1 ));
            $ext =   new \Zend\Validator\File\Extension(array('pdf'));
            $httpadapter->setValidators(array($filesize,$ext));

            if($httpadapter->isValid()) {

                $httpadapter->setDestination(TMP_DIR);

                if($httpadapter->receive()) {

                    $filterRaname = new \Zend\Filter\File\Rename(array(
                        "randomize" => true,
                    ));
                    echo json_encode(basename(($filterRaname->filter($httpadapter->getFileName()))));


                }else{


                    exit;
                }
            }

            exit;
        }

        exit;
    }

}
