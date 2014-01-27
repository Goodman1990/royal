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
//        $events->attach('dispatch', array($this, 'postDispatch'), -100);
    }
    public function preDispatch (MvcEvent $e){
        $this->Page = new Page();
        $this->layout('layout/layoutAdmin');
        $this->layout()->setVariables(array('page'=>$this->Page));

    }
//    public function PostDispatch (MvcEvent $e){
//
//
//    }

    public function editCategoryAction()
    {
        $this->request = $this->getRequest();

        $id_page = $this->params()->fromRoute('id_page', 0);

        if($id_page=='page'){
            $model = \Royal\Models\CategoryPagesModel::model();
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.2'
            )));
        }else{
            $this->Page->setActivePage(array('admin'=>array(
                'tab'=>'1',
                'sub'=>'1.1'
            )));
            $model = \Royal\Models\CategoriesProductModel::model();
        }
        $categoryData = $model->findAllOrder('number DESK ');//->addOrder('DESK number')->customExecute();

        $formEdit = new formGenerate('editCategory', 'standart grouped');
        $formEdit->setMultiFormEdit($model->rules(), $categoryData);
        $formAdd = new formGenerate('addCategory', 'standart grouped', $model);

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
                    }

                    $formEdit->setData($validData);
                }

            } else {

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

    public function imageAction() {


        $request =new Request();
        $this->Page->setActivePage(array('admin'=>array(
            'tab'=>'1',
            'sub'=>'1.2'
        )));
//        $this->layout()->setVariables(array('page'=>$this->Page));
        if($request->isPost()){

            $httpadapter = new \Zend\File\Transfer\Adapter\Http();
            $filesize  = new \Zend\Validator\File\Size(array('min' => 1 ));
            $ext =   new \Zend\Validator\File\Extension(array('png', 'jpg'));
            $httpadapter->setValidators(array($filesize,$ext));
            if($httpadapter->isValid()) {

                $httpadapter->setDestination(TMP_DIR);

                if($httpadapter->receive()) {
                    $filterRaname = new \Zend\Filter\File\Rename(array(
                        "randomize" => true,
                    ));
                    $filterRaname->filter($httpadapter->getFileName());
                }
            }

        }

        return new ViewModel(array(
                '1'=>'1'
        ));


    }

}
