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
use watermark\watermark;
use Royal\helpers\PaginationHelpers;
use Royal\helpers\AuthHelper;
use Zend\Session\Container;
use Zend\Session\SessionManager;
//use Zend\Authentication\Adapter\DbTable as AuthAdapter;


class adminController extends AbstractActionController
{

    public $adapter;
    public $request;
    public $validData;
    public $Page;
    public $action;
    public $controller;
    public $AuthHelper;


    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach('dispatch', array($this, 'preDispatch'), 100);
    }
    public function preDispatch (MvcEvent $e){

        $this->request = $this->getRequest();
        $this->Page = new Page();
        $route = $this->getParamsCustom();
        $this->Page->controller = $route['__CONTROLLER__'];
        $this->Page->action = $route['action'];
        $this->AuthHelper =  new AuthHelper($this->getServiceLocator()->get('Zend\Session\SessionManager'));


        if(!$this->AuthHelper->isLogin() && $this->Page->action!='index'){

            $this->redirect()->toRoute('Royal',array(
                'controller'=>'admin',
                'action'=>'index',
            ));

        }
        $this->layout('layout/layoutAdmin');
        $this->layout()->setVariables(array('page'=>$this->Page));


    }

    private function getParamsCustom(){

        return  $route = $this->getEvent()->getRouteMatch()->getParams();
    }
    private function getAdapter(){

        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    private function getRouteParam() {
        $routeParam = explode('/',$this->getRequest()->getUri()->toString());
        for($i=5;$i<count($routeParam);$i++){
            $this->Page->paramRoute[] =  $routeParam[$i];
        }
    }



    public function indexAction() {


        if($this->AuthHelper->isLogin()){

            $this->redirect()->toRoute('Royal',array(
                'controller'=>'admin',
                'action'=>'addProduct',
            ));

        }
        $this->Page->setRenderMenu(false);
        $form = new formGenerate('loginUser','loginUser',array(
            'email'=>array('validators'=>array('email'), 'required' => true,'setLabel' => 'Логин'),
            'password'=>array('validators'=>false, 'required' => true,'typeInput'=>'password', 'setLabel' => 'Пароль'),

        ));

        if($this->request->isPost()){

            $post =  $this->request->getPost();
            $form->setData($post);

            if($form->isValid()){

                if($this->AuthHelper->auth($post)){

                    $this->redirect()->toRoute('Royal',array(
                        'controller'=>'admin',
                        'action'=>'index',
                    ));

                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));

    }

    public function editCategoryAction()
    {

        $id_page = $this->params()->fromRoute('param1', 0);
        $this->getRouteParam();

        if($id_page=='page'){


            $model = \Royal\Models\CategoryPagesModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'3',
                'sub'=>'3.1'
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
                'sub'=>'1.2'
            )));
            $rowTable = 'id_categories_product';

            if(count($categoryData)==0){

                $this->redirect()->toRoute('Royal',array(
                    'controller'=>'admin',
                    'action'=>'editCategory',
                    'param1'=>'product'
                ));

            }

        }else{

            $categoryData = \Royal\Models\SubcategoriesProductModel::model()->findAllOrder('number DESK ');
            $model = \Royal\Models\ManufacturersModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.3'
            )));
            $rowTable = 'id_subcategories_product';

            if(count($categoryData)==0){

                $this->redirect()->toRoute('Royal',array(
                    'controller'=>'admin',
                    'action'=>'editSubcategories',
                    'param1'=>'subcategories'
                ));

            }
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

                        if(file_exists( TMP_DIR.$this->validData['image_' . $i])){
                            rename(TMP_DIR.$this->validData['image_' . $i],SITE_DIR.'categories/'.$this->validData['image_' . $i]);
                            unlink(SITE_DIR.'categories/'.$oldData['image_' . $i]);
                        }



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

        $id_categories_product = $this->params()->fromRoute('param1', 0);

        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'2',
            'sub'=>'2.1'
        )));

        $SubcategoriesProductModel =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true));
        $CategoriesProductModel =  \Royal\Models\CategoriesProductModel::model(array('asArray'=>true));
        $ManufacturersModel = \Royal\Models\ManufacturersModel::model(array('asArray'=>true));
        $ProductModel =  \Royal\Models\ProductModel::model();
        $colorsModel = \Royal\Models\ColorsModel::model();

        $CategoriesProductData =  $CategoriesProductModel->findAllOrder('number DESK ');

        $formAddProduct= new formGenerate('addProduct', 'category addProduct');
        $formAddColor= new formGenerate('addColor', 'category addProduct color');

        $rules =  $ProductModel->rules();

        $categoriesData = $CategoriesProductModel->findAllOrder('number DESK ');


        if($this->request->isPost()){

            $SubcategoriesProductData =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByAttributes(array(
                'id_categories_product'=>(int)$this->request->getPost()->id_categories_product,
            ));
            $manufacturers = $ManufacturersModel->findByAttributes(array(
                'id_subcategories_product'=>(int)$this->request->getPost()->id_subcategories_product,
            ));

        }else{

            $SubcategoriesProductData =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByAttributes(array(
                'id_categories_product'=>$categoriesData[0]['id'],
            ));

            $manufacturers = $ManufacturersModel->findByAttributes(array(
                'id_subcategories_product'=>$SubcategoriesProductData['id']
            ));
        }


        $rules['id_manufacturers']['selectInfo'] = $manufacturers;
        $rules['id_subcategories_product']['selectInfo'] = $SubcategoriesProductData;
        $rules['id_categories_product']['selectInfo'] = $categoriesData;
        $rules['id_manufacturers']['typeInput'] = 'select';
        $rules['id_categories_product']['typeInput'] = 'select';
        $rules['id_subcategories_product']['typeInput'] = 'select';
        $rules['id_manufacturers']['setLabel'] = 'Производитель';
        $rules['id_categories_product']['setLabel'] = 'Категории';
        $rules['id_subcategories_product']['setLabel'] = 'Подкатегории';



        $formAddProduct->setDataForm($rules);
        $formAddColor->setDataForm($colorsModel->rules());

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();

            $formAddProduct->setData($Post);
            $formAddColor->setData($Post);

            if($formAddProduct->isValid() && $formAddColor->isValid()){

                $this->validData = $formAddProduct->getData();
                $color  = $formAddColor->getData();

                $image =explode(',',$this->validData['image']);

                if($this->validData['main_image']==''){

                    rename('public'.$image[0],SITE_DIR.'/product/'.basename($image[0]));
                    rename('public/tmp/large/'.basename($image[0]),SITE_DIR.'product/large/'.basename($image[0]));
                    $this->validData['main_image']= '/siteDir/product/'.basename($image[0]);
                    unset($image[0]);
                    $image = array_values($image);

                }

                if($image!=Null){

                    for($i = 0;$i<count($image);$i++){

                        rename('public'.$image[$i],SITE_DIR.'/product/'.basename($image[$i]));
                        rename('public/tmp/large/'.basename($image[$i]),SITE_DIR.'product/large/'.basename($image[0]));
                        $arrImage[] = '/siteDir/product/'.basename($image[$i]);
                    }

                    $this->validData['image'] = implode(',',$arrImage);

                }else{

                    $this->validData['image'] ='';

                }

                $file =explode(',',$this->validData['file']);
                $arrFile = '';

                for($i = 0;$i<count($file);$i++){
                    rename(TMP_DIR.$file[$i],SITE_DIR.'/product/file/'.basename($file[$i]));
                    $arrFile[] = '/siteDir/product/file/'.basename($file[$i]);
                }

                $this->validData['file'] = implode(',',$arrFile);

                $video = explode(',',$this->validData['video']);
                $hashVideo ='';

                for($i = 0;$i<count($video);$i++){

                    $buf = explode('=',$video[$i]);
                    $hashVideo[] = array_pop($buf);
                }

                $this->validData['video'] = implode(',',$hashVideo);
                unset($this->validData['id']);
                $ProductModel->setAttributes($this->validData)->save();
                $id_product = $ProductModel->getLastInsertValue();

                $colorValue = explode(',',$color['color']);
                $colorImageValue = explode(',',$color['image_color']);

                for($i = 0;$i<count($colorImageValue);$i++){

                    rename('public'.$colorImageValue[$i],SITE_DIR.'product/color/'.basename($colorImageValue[$i]));

                    $arrImageValue[$i] = '/siteDir/product/color/'.basename($colorImageValue[$i]);
                    $colorsModel->setAttributes(
                        array('color'=>$colorValue[$i],'image_color'=>$arrImageValue[$i],'id_product'=>$id_product)
                    )->save();
                }

                $this->redirect()->toRoute('Royal',array(
                    'controller'=>'admin',
                    'action'=>'addProduct',
                ));

            }
        }
        return new ViewModel(array(
            'formAdd'=>$formAddProduct,
            'formAddColor'=>$formAddColor,
//            'id_subcategories_product'=>$id_categories_product,
//            'categories_product_id'=>$id_categories_product


        ));
    }

    public function editPageAction() {

        $id_page = $this->params()->fromRoute('param1', 0);

        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'3',
            'sub'=>'3.2'
        )));
        $CategoryPagesModel =  \Royal\Models\CategoryPagesModel::model(array('asArray'=>true))->findAllOrder('number DESK');

        if($id_page == 0){
            $id_page  = $CategoryPagesModel[0]['id'];
        }
        $this->Page->addTab($CategoryPagesModel,$id_page,true);
        $MainPagesModel =  \Royal\Models\MainPagesModel::model();
        $formEditPage = new formGenerate('editPage', 'editPage');
        $formEditPage->setDataForm($MainPagesModel->rules());
        $dataPage = $MainPagesModel->findByAttributes(array('id_category_pages'=>$id_page));
        $formEditPage->setData($MainPagesModel->getAttributes());
        if($this->request->isPost()){
            $post = $this->request->getPost();
            $formEditPage->setData($post);
            if($formEditPage->isValid()){
                $this->validData = $formEditPage->getData();
                if($dataPage==array()){
                    unset($this->validData['id']);
                }
                $MainPagesModel->setAttributes($this->validData)->save();
                $dataPage = $MainPagesModel->findByAttributes(array('id_category_pages'=>$id_page));
            }
        }

        return new ViewModel(array(
            'formEditPage'=>$formEditPage,
            'MainPagesModel'=>$dataPage,
            'id_page'=>$id_page
        ));

    }

    public function editProductAction(){


        $id_product = $this->params()->fromRoute('param1', 0);

        if($id_product==0){

            $this->redirect()->toRoute('Royal',array(
                'controller'=>'admin',
                'action'=>'getAllproduct',
            ));

        }

        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'2',
            'sub'=>'2.1'
        )));


        $ManufacturersModel = \Royal\Models\ManufacturersModel::model(array('asArray'=>true));
        $ProductModel =  \Royal\Models\ProductModel::model()->findByPk($id_product);
        $colorsModel = \Royal\Models\ColorsModel::model(array('asArray'=>true));

        $colorData = $colorsModel
            ->findByAttributes(array('id_product'=>$id_product));

        $formAddProduct= new formGenerate('addProduct', 'category addProduct');
        $formAddColor= new formGenerate('addColor', 'category addProduct color');
        $rules =  $ProductModel->rules();

        $ProductModel->findByPk($id_product);
        $categories = \Royal\Models\CategoriesProductModel::model(array('asArray'=>true))->findAllOrder('number DESK ');

        if($this->request->isPost()){

            $SubcategoriesProductData =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByAttributes(array(
                'id_categories_product'=>(int)$this->request->getPost()->id_categories_product,
            ));
            $manufacturers = $ManufacturersModel->findByAttributes(array(
                'id_subcategories_product'=>(int)$this->request->getPost()->id_subcategories_product,
            ));

        }else{

            $SubcategoriesProductData =  \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByAttributes(array(
                'id_categories_product'=>$ProductModel->id_categories_product,
            ));
            $manufacturers = $ManufacturersModel->findByAttributes(array(
                'id_subcategories_product'=>$ProductModel->id_subcategories_product
            ));
        }


        $rules['id_manufacturers']['selectInfo'] = $manufacturers;
        $rules['id_subcategories_product']['selectInfo'] = $SubcategoriesProductData;
        $rules['id_categories_product']['selectInfo'] = $categories;
        $rules['id_manufacturers']['typeInput'] = 'select';
        $rules['id_categories_product']['typeInput'] = 'select';
        $rules['id_subcategories_product']['typeInput'] = 'select';
        $rules['id_manufacturers']['setLabel'] = 'Производитель';
        $rules['id_categories_product']['setLabel'] = 'Категории';
        $rules['id_subcategories_product']['setLabel'] = 'Подкатегории';

        $formAddProduct->setDataForm($rules);
        $formAddColor->setDataForm($colorsModel->rules());

        foreach ($colorData as $key) {
            $colorForm['color'][] = $key['color'];
            $colorForm['image_color'][] = $key['image_color'];
        }

        $colorForm['color'] = implode(',',$colorForm['color']);
        $colorForm['image_color'] = implode(',',$colorForm['image_color']);
        $colorForm['id_product'] = $id_product;

        $formAddColor->setData($colorForm);
        $formAddProduct->setData($ProductModel->getAttributes());

        if ($this->request->isPost()) {

            $arrImage= '';
            $Post = $this->request->getPost()->toArray();
            $formAddProduct->setData($Post);
            $formAddColor->setData($Post);


            if($formAddProduct->isValid() && $formAddColor->isValid()){

                $this->validData = $formAddProduct->getData();
                $color  = $formAddColor->getData();

                $image =explode(',',$this->validData['image']);

                if($this->validData['main_image']==''){

                    if(file_exists('public/tmp/'.basename($image[0]))){
                        rename('public'.$image[0],SITE_DIR.'/product/'.basename($image[0]));
                        rename('public/tmp/large/'.basename($image[0]),SITE_DIR.'product/large/'.basename($image[0]));
                    }
                    $this->validData['main_image'] = '/siteDir/product/'.basename($image[0]);
                    unset($image[0]);
                    $image = array_values($image);

                }

                if($image!=Null){

                    for($i = 0;$i<count($image);$i++){

                        if(file_exists('public/tmp/'.basename($image[$i]))){
                            rename('public'.$image[$i],SITE_DIR.'product/'.basename($image[$i]));
                            rename('public/tmp/large/'.basename($image[$i]),SITE_DIR.'product/large/'.basename($image[$i]));
                        }
                        $arrImage[] = '/siteDir/product/'.basename($image[$i]);
                    }

                    $this->validData['image'] = implode(',',$arrImage);

                }else{

                    $this->validData['image'] ='';

                }

                $file =explode(',',$this->validData['file']);
                $arrFile = '';

                for($i = 0;$i<count($file);$i++){

                    if(file_exists('public/tmp/'.basename($file[$i]))){
                        rename(TMP_DIR.$file[$i],SITE_DIR.'/product/file/'.basename($file[$i]));
                    }

                    $arrFile[] = '/siteDir/product/file/'.basename($file[$i]);
                }

                $this->validData['file'] = implode(',',$arrFile);
                $video = explode(',',$this->validData['video']);
                $hashVideo ='';

                for($i = 0;$i<count($video);$i++){

                    $buf = explode('=',$this->validData['video']);
                    $hashVideo[] = end($buf);
                }

                $this->validData['video'] = implode(',',$hashVideo);
                $this->validData['id']=$id_product;
                $ProductModel->setAttributes($this->validData)->save();
//                $id_product = $ProductModel->getLastInsertValue();

                $colorValue = explode(',',$color['color']);
                $colorImageValue = explode(',',$color['image_color']);
                $arrImageValue='';
                for($i = 0;$i<count($colorImageValue);$i++){

                    if(file_exists('public/tmp/'.basename($colorImageValue[$i]))){

                        rename('public/tmp/'.basename($colorImageValue[$i]),SITE_DIR.'product/color/'.basename($colorImageValue[$i]));
                        $arrImageValue[$i] = '/siteDir/product/color/'.basename($colorImageValue[$i]);
                        $colorsModel->setAttributes(
                            array('color'=>$colorValue[$i],'image_color'=>$arrImageValue[$i],'id_product'=>$id_product)
                        )->save();

                    }else{
                        $arrImageValue[$i] = $colorImageValue[$i];
                    }

                }

                $formAddProduct->get('image')->setValue($this->validData['image']);
                $formAddColor->get('image_color')->setValue(implode(',',$arrImageValue));

            }
        }
        return new ViewModel(array(
            'formAdd'=>$formAddProduct,
            'productModel'=>$ProductModel,
            'formAddColor'=>$formAddColor
        ));

    }

    public function getAllproductAction() {

        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'2',
            'sub'=>'2.2'
        )));
        $id_categories_product = $this->params()->fromRoute('param1', 0);

        $categoryProduct = \Royal\Models\CategoriesProductModel::model()->findAllOrder('number DESK');

        if($id_categories_product==0){
            $id_categories_product = $categoryProduct[0]['id'];
        }

        $subcategoriesData = \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))->findByAttributes(array(
            'id_categories_product'=> $id_categories_product
        ));

        $this->Page->addTab($categoryProduct,$id_categories_product,true);
        $paginationHelper = new PaginationHelpers($this->request->getPost());

        if($this->request->isPost()){

            $post  = $this->request->getPost();

            if($paginationHelper->search){

                $productData = \Royal\Models\ProductModel::model(array('asArray'=>true))
                    ->getProduct($paginationHelper);

                $manufacturersData = \Royal\Models\ManufacturersModel::model(array('asArray'=>true))
                    ->findByAttributes($post);

            }else{

                if($post['name']=='id_subcategories_product'){
                  
                    $manufacturersData = \Royal\Models\ManufacturersModel::model(array('asArray'=>true))
                        ->findByAttributes(array(
                            $post['name']=> $post['value']
                        ));
                }
                    $productData = \Royal\Models\ProductModel::model(array('asArray'=>true))
                        ->findByAttributes(array( $post['name']=> $post['value']));


            }

        }else{


            $manufacturersData = \Royal\Models\ManufacturersModel::model(array('asArray'=>true))
                ->findByAttributes(array(
                    'id_subcategories_product'=> $subcategoriesData[0]['id']
                ));
            $productData = \Royal\Models\ProductModel::model(array('asArray'=>true))
                ->findByAttributes(array('id_categories_product'=> $id_categories_product));

        }


        return new ViewModel(array(
            'productData'=>$productData,
            'manufacturersData'=>$manufacturersData,
            'subcategoriesData'=>$subcategoriesData,
            'categoryProduct'=>$categoryProduct
        ));
    }




    public function cropAction() {

        $dataImage = $this->getRequest()->getPost()->toArray();
        $imageHelper  = new imageHelper(TMP_DIR.$dataImage['imageName']);

        if(isset($dataImage['large']))
            $imageHelper->save(TMP_DIR.'large/'.$dataImage['imageName']);

        if($dataImage['w']!=0 && $dataImage['h']!=0){
            $imageHelper->resize($dataImage['width'],$dataImage['height']);
            $imageHelper->cut($dataImage['x1'],$dataImage['y1'],$dataImage['w'],$dataImage['h']);
        }

        $imageHelper->save();

        if($dataImage['marker']=='1'){

            $imageHelper->watermark(SITE_DIR.'woterMark3.png');

        }if($dataImage['mainColor']=='1'){

             $color = $imageHelper->getMainColorImage();
             echo json_encode(array('color'=>$color,'imageName'=>$dataImage['imageName']));
             exit;
        }

        echo json_encode(array('imageName'=>$dataImage['imageName']));
        exit;

    }

    public function deletedProductAction() {

        $id_product = $this->params()->fromRoute('param1', 0);
        \Royal\Models\ProductModel::model(array('asArray'=>true))->delete(array('id'=>$id_product));
        echo 1;
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


    public function uploadFileAction() {

        $this->layout()->setVariables(array('page'=>$this->Page));
        $request =new Request();

        if($request->isPost()){

            $httpadapter = new \Zend\File\Transfer\Adapter\Http();
            $filesize  = new \Zend\Validator\File\Size(array('min' => 1 ));
            $ext =   new \Zend\Validator\File\Extension(array('pdf','xlsx','xls'));
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

    public function uploadImageColorAction() {

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

    public function getManufacturersAction(){

        if($this->request->isPost() &&$this->request->isXmlHttpRequest()){
            $post = $this->request->getPost();
                $ManufacturersData = \Royal\Models\ManufacturersModel::model(array('asArray'=>true))
                ->findByAttributes(array(
                        $post['name']=>$post['value']
                ));
            echo json_encode($ManufacturersData);
            exit;
        }

    }

    public function getSubcategoriesAction(){

        if($this->request->isPost() &&$this->request->isXmlHttpRequest()){
            $post = $this->request->getPost();
            $ManufacturersData = \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))
                ->findByAttributes(array(
                    $post['name']=>$post['value']
                ));
            echo json_encode($ManufacturersData);
            exit;
        }

    }



    public function deletedFilesAction(){

        if($this->request->isPost() && $this->request->isXmlHttpRequest()){
           $post =  $this->request->getPost()->toArray();
            if(isset($post['table']) && $post['table'] !='Colors'){
                $model = '\Royal\Models\\'.$post['table'].'Model';
                $currentModel = $model::model()->setAttributes(array('id'=>$post['id_product'],$post['row']=>$post['data']))->save();

            }else if(isset($post['table']) && $post['table'] =='Colors'){

                $model = '\Royal\Models\\'.$post['table'].'Model';
                $currentModel = $model::model()->delete(array('id_product'=>$post['id_product'],'image_color'=>$post['src'],'color'=>$post['color']));

            }
            unlink('public'.$post['src']);
        }
        echo 1;
        exit;

    }


    public function setPopupCookie($popupParams){
        $TTL = 360;
        $cookieTime = time() + $TTL;
        return setcookie(
            'popup',
            json_encode($popupParams),
            $cookieTime,
            '/'
        //,$_SERVER['SERVER_NAME']
        //$config->getCookieDomain(),
        //$config->getCookieSecure(),
        //$config->getCookieHttpOnly()
        );
    }

}
