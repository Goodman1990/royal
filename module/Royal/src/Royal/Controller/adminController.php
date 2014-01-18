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
use Royal\helpers\generalHelper;


class adminController extends AbstractActionController
{
    public $adapter;
    public $request;
    public $validData;

    public function editCategoryAction()
    {
        $this->request = $this->getRequest();
        $CategoryPagesModel = \Royal\Models\CategoryPagesModel::model();
        $categoryPageData = $CategoryPagesModel->getAll();
        $formEdit = new formGenerate('editCategory', 'standart grouped');
        $formEdit->setMultiFormEdit($CategoryPagesModel, $categoryPageData);
        $formAdd = new formGenerate('addCategory', 'standart grouped', $CategoryPagesModel);

        if ($this->request->isPost()) {

            $Post = $this->request->getPost()->toArray();

            if (isset($Post['edit'])) {

                $formEdit->setData($Post);
                if ($formEdit->isValid()) {

                    $this->validData = $formEdit->getData();

                    for ($i = 0; $i < $formEdit->countInput; $i++) {

                        \Royal\Models\CategoryPagesModel::model()
                            ->setAttributes(array(
                                'id' => $this->validData['id_' . $i],
                                'title' => $this->validData['title_' . $i],
                            ))->save();
                    }

                }

            } else {

                $formAdd->setData($Post);

                if ($formAdd->isValid()) {

                    $this->validData = $formAdd->getData();
                    $id = \Royal\Models\CategoryPagesModel::model()
                        ->setAttributes(array(
                            'title' => $this->validData['title'],
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
            'categoryPageData' => $categoryPageData
        ));
    }


}
