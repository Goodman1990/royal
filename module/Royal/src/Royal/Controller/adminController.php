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

    public function editAction()
    {
        $this->request = $this->getRequest();

            $categoryDataW = \Royal\Models\CategoryPagesModel::model()->getAll();
            $formEdit = new formGenerate('editCategory', 'standart grouped');
            $i = 1;
            foreach ($categoryDataW as $key => $value) {
                $i++;
                $formEdit->setDataForm(array(
                    'title_' . $i =>
                        array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
                    'id_' . $i =>
                        array('required' => true, 'typeInput' => 'hidden', 'validators' => array('regex' => 'numbers'), 'filters' => array('trim', 'int', 'tag'))
                ), null, $i);
                $categoryPage['title_' . $i] = $value['title'];
                $categoryPage['id_' . $i] = $value['id'];
            }
            $formAdd = new formGenerate('addCategory', 'standart grouped',
                array(
                    'title' =>
                        array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
                ));
        if ($this->request->isPost()) {
            $Post = $this->request->getPost()->toArray();
            $formEdit->setData($Post);
            $formAdd->setData($Post);
            if ($formEdit->isValid() && $Post['edit']!==null) {
                $this->validData = $formEdit->getData();
                for ($i = 2; $i <= $formEdit->iteratorss; $i++) {
                    \Royal\Models\CategoryPagesModel::model()
                        ->setAttributes(array(
                            'id' => $this->validData['id_' . $i],
                            'title' => $this->validData['title_' . $i],
                        ), true)->save();
                }
            } else if($formAdd->isValid() && $Post['add']!==null){
                $this->validData = $formAdd->getData();
              $id =   \Royal\Models\CategoryPagesModel::model()
                    ->setAttributes(array(
                        'title' => $this->validData['title'],
                    ), true)->save();
                $i = $formEdit->iteratorss+1;
                $formEdit->setDataForm(array(
                    'title_' . $i =>
                        array('required' => true, 'validators' => array('regex' => 'numbers_letters',), 'setLabel' => 'название категории'),
                    'id_' . $i =>
                        array('required' => true, 'typeInput' => 'hidden', 'validators' => array('regex' => 'numbers'), 'filters' => array('trim', 'int', 'tag'))
                ),null,$i);
                $categoryPage['title_'.$i] = $this->validData['title'];
                $categoryPage['id_'.$i] = $id;
                $formEdit->setData($categoryPage);
                $formAdd->clearElements();
                $categoryDataW = \Royal\Models\CategoryPagesModel::model()->getAll();
            }

        } else {
            $formEdit->setData($categoryPage);
        }
        return new ViewModel(array(
            'formEdit' => $formEdit,
            'formAdd'=>$formAdd,
            'categoryDataW' => $categoryDataW
        ));
    }



}
