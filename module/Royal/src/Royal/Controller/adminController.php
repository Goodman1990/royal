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


    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach('dispatch', array($this, 'preDispatch'), 100);
    }
    public function preDispatch (MvcEvent $e){
        $this->request = $this->getRequest();
        $this->Page = new Page();
        $this->layout('layout/layoutAdmin');
        $this->layout()->setVariables(array('page'=>$this->Page));

    }

    public function editCategoryAction()
    {
        $id_page = $this->params()->fromRoute('id_page', 0);

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

                $formEdit->setData($Post);

                if ($formEdit->isValid()) {

                    $this->validData = $formEdit->getData();
                    for ($i = 0; $i < $formEdit->countInput; $i++) {

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

    public function subcategoriesAction() {

//        if(!$this->request->isPost()){
        $categoryData = \Royal\Models\CategoriesProductModel::model()->findAllOrder('number DESK ');
        $model = \Royal\Models\SubcategoriesProductModel::model();

//        var_dump($categoryData);
        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'1',
            'sub'=>'1.3'
        )));
        $id_page = $this->params()->fromRoute('id_page', 0);
        if($id_page==0){
            $id_page = $categoryData[0]['id'];
        }
//        }
        $subcategoriesData = \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))
            ->findByAttributes(array('id_categories_product'=>$id_page));
        $this->Page->addTab($categoryData,$id_page,true);
        $formEdit = new formGenerate('editSubCategory', 'category');
        $formEdit->setMultiFormEdit($model->rules(), $subcategoriesData);
        $formAdd = new formGenerate('addSubCategory', 'category', $model);

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();

            if (isset($Post['edit'])) {

                $formEdit->setData($Post);
                if ($formEdit->isValid()) {

                    $this->validData = $formEdit->getData();

                    for ($i = 0; $i < $formEdit->countInput; $i++) {
                        $model::model()
                            ->setAttributes(array(
                                'id' => $this->validData['id_' . $i],
                                'id_categories_product' => $this->validData['id_categories_product_' . $i],
                                'title' => $this->validData['title_' . $i],
                                'number'=>$this->validData['number_' . $i],
                                'visible'=>$this->validData['visible_' . $i],
                                'image'=>$this->validData['image_' . $i]
                            ))->save();
                       
                        $k = $this->validData['number_'.$i]-1;
                        $validData['id_'.$k] = $this->validData['id_'.$i];
                        $validData['title_'.$k] = $this->validData['title_'.$i];
                        $validData['number_'.$k] = $this->validData['number_'.$i];
                        $validData['visible_'.$k] = $this->validData['visible_'.$i];
                        $validData['image_'.$k] = $this->validData['image_'.$i];

//
                    }

                    $subcategoriesData = \Royal\Models\SubcategoriesProductModel::model(array('asArray'=>true))
                        ->findByAttributes(array('id_categories_product'=>$id_page));
                    $formEdit->setData($subcategoriesData);
                }

            } else {

                $formAdd->setData($Post);

                if ($formAdd->isValid()) {
                    $this->validData = $formAdd->getData();
                    unset($this->validData['id']);
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
            'id_page'=>$id_page
        ));


    }

    public function getController() {
        $controller =  explode('\\',$this->getEvent()->getRouteMatch()->getParams());
        return mb_strtolower(end($controller));
    }

    public function cropAction() {

        $dataImage = $this->getRequest()->getPost()->toArray();
        $imageHelper  = new imageHelper(TMP_DIR.$dataImage['imageName']);
        $imageHelper->resize($dataImage['width'],$dataImage['height']);
        $imageHelper->cut($dataImage['x1'],$dataImage['y1'],$dataImage['w'],$dataImage['h']);
        $imageHelper->save();
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
}
