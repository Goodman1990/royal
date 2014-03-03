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
use Royal\helpers\paginationHelpers;


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

    public function indexAction() {


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

        if($id_categories_product==0){
            $id_categories_product = $CategoriesProductData[0]['id'];
        }
        $SubcategoriesProductData = $SubcategoriesProductModel->findByAttributes(array(
            'id_categories_product'=>$id_categories_product
        ));
       $this->Page->addTab($CategoriesProductData,$id_categories_product,true);


        if($this->request->isPost()){
            $manufacturers= $ManufacturersModel->findByAttributes(array(
                'id_subcategories_product'=>(int) $this->request->getPost()->id_subcategories_product
            ));
            $rules['id_manufacturers']['selectInfo'] = $manufacturers;
            $rules['id_manufacturers']['typeInput'] = 'select';
        }
            $rules['id_subcategories_product']['selectInfo'] = $SubcategoriesProductData;
            $rules['id_subcategories_product']['typeInput'] = 'select';
            $rules['id_manufacturers']['typeInput'] = 'select';

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
                $arrImage = '';

                for($i = 0;$i<count($image);$i++){
                    rename('public'.$image[$i],SITE_DIR.'/product/'.basename($image[$i]));
                    $arrImage[] = '/siteDir/product/'.basename($image[$i]);
                }

                $this->validData['image'] = implode(',',$arrImage);
                $file =explode(',',$this->validData['file']);
                $arrFile = '';
//                var_dump($file);
//                exit;
                for($i = 0;$i<count($file);$i++){
                    rename(TMP_DIR.$file[$i],SITE_DIR.'/product/file/'.basename($file[$i]));
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
                unset($this->validData['id']);
                $ProductModel->setAttributes($this->validData)->save();
                $id_product = $ProductModel->getLastInsertValue();

                $colorValue = explode(',',$color['color']);
                $colorImageValue = explode(',',$color['image_color']);

                for($i = 0;$i<count($colorImageValue);$i++){
                    rename('public'.$colorImageValue[$i],SITE_DIR.'/product/color/'.basename($colorImageValue[$i]));
                    $arrImageValue[$i] = '/siteDir/product/color/'.basename($colorImageValue[$i]);
                    $colorsModel->setAttributes(
                        array('color'=>$colorValue[$i],'image_color'=>$arrImageValue[$i],'id_product'=>$id_product)
                    )->save();
                }

                 echo'<pre>';
                 var_dump($Post);
                 exit;
              $this->redirect()->toRoute('Royal',array(
                  'controller'=>'admin',
                  'action'=>'index',
              ));

            }else{

              $ManufacturersData = $ManufacturersModel->findByAttributes(array(
                    'id_subcategories_product'=>$Post['id_subcategories_product']
                ));
                $rules['id_manufacturers']['selectInfo'] = $ManufacturersData;
                $rules['id_manufacturers']['typeInput'] = 'select';

                $formAddProduct->setDataFormAdd(array('id_manufacturers'=>$rules['id_manufacturers']));

            }
        }
        return new ViewModel(array(
            'formAdd'=>$formAddProduct,
            'formAddColor'=>$formAddColor,
            'id_subcategories_product'=>$id_categories_product,
            'categories_product_id'=>$id_categories_product


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

        $formAddProduct->setData($ProductModel->getAttributes());

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();
            $formAddProduct->setData($Post);

            if($formAddProduct->isValid()){

                $this->validData = $formAddProduct->getData();


//                $image =explode(',',$this->validData['image']);
//                for($i = 0;$i<count($image);$i++){
//                    rename(TMP_DIR.$image[$i],SITE_DIR.'/product/'.$image[$i]);
//                }
//                $video = explode(',',$this->validData['video']);
//                $hashVideo ='';
//                for($i = 0;$i<count($video);$i++){
//                    $buf = explode('=',$this->validData['video']);
//                    $hashVideo[] = end($buf);
//                }
//                $this->validData['video'] = implode(',',$hashVideo);





                $image =explode(',',$this->validData['image']);
                $arrImage = '';

                for($i = 0;$i<count($image);$i++){

                    if(file_exists(TMP_DIR.$image[$i])){
                        rename(TMP_DIR.$image[$i],SITE_DIR.'/product/'.$image[$i]);
                    }
                    $arrImage[] = SITE_DIR.'/product/'.$image[$i];
                }
                $this->validData['image'] = implode(',',$arrImage);

                $file =explode(',',$this->validData['file']);
                $arrFile = '';

                for($i = 0;$i<count($file);$i++){
                    if(file_exists(TMP_DIR.$file[$i])){
                        rename(TMP_DIR.$image[$i],SITE_DIR.'/product/file/'.$file[$i]);
                    }
                    $arrFile[] = SITE_DIR.'/product/'.$image[$i];
                }
                $this->validData['file'] = implode(',',$arrFile);

                $video = explode(',',$this->validData['video']);
                $hashVideo ='';

                for($i = 0;$i<count($video);$i++){
                    $buf = explode('=',$this->validData['video']);
                    $hashVideo[] = end($buf);
                }
                $this->validData['video'] = implode(',',$hashVideo);

                $this->validData['id'] = $id_product;

                $ProductModel->setAttributes($this->validData)->save();
            }
        }
        return new ViewModel(array(
            'formAdd'=>$formAddProduct,
            'productModel'=>$ProductModel
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
        $paginationHelper = new paginationHelpers($this->request->getPost());
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
//                echo'<pre>';
//                var_dump($productData);
//                exit;
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
        if($dataImage['w']!=0 && $dataImage['h']!=0){
            $imageHelper->resize($dataImage['width'],$dataImage['height']);
            $imageHelper->cut($dataImage['x1'],$dataImage['y1'],$dataImage['w'],$dataImage['h']);
        }
        $imageHelper->save();
        if($dataImage['marker']=='1'){
            $imageHelper->watermark(SITE_DIR.'woterMark3.png');
        }if($dataImage['marker']=='1'){
          $color = $imageHelper->getMainColorImage();
             echo json_encode(array('color'=>$color,'imageName'=>$dataImage['imageName']));
            exit;
        }

        echo json_encode($dataImage['imageName']);
        exit;

    }

    public function deletedProductAction() {

        $id_product = $this->params()->fromRoute('param1', 0);
        \Royal\Models\ProductModel::model(array('asArray'=>true))->delete(array('id'=>$id_product));
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

// /tmp/<br /> <b>Warning</b>: imagecreatetruecolor(): Invalid image dimensions in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>138</b><br /> <br /> <b>Warning</b>: imagecolorallocate() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>140</b><br /> <br /> <b>Warning</b>: imagecolortransparent() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>140</b><br /> <br /> <b>Warning</b>: imagealphablending() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>141</b><br /> <br /> <b>Warning</b>: imagesavealpha() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>142</b><br /> <br /> <b>Warning</b>: imagecopy() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>144</b><br /> <br /> <b>Warning</b>: imagejpeg() expects parameter 1 to be resource, boolean given in <b>E:\phpProject\royalBRG\module\Royal\src\Royal\helpers\imageHelper.php</b> on line <b>46</b><br /> 383005-1366x768_530498b187e3a.jpg
    public function uploadFileAction() {

        $this->layout()->setVariables(array('page'=>$this->Page));
        $request =new Request();
        if($request->isPost()){
//            $helper = new generalHelper();
//            $file = $request->getFiles();
//            echo'<pre>';
//            var_dump($file['pdf-file'][0]['name']);
//            exit;

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
        if($this->request->isPost() &&$this->request->isXmlHttpRequest()){
            $post = $this->request->getPost();
            unlink('public'.$post['src']);
            exit;
        }
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
