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
       $categoryDataW = \Royal\Models\CategoryPagesModel::model()->getAll();

        $form = new formGenerate('editCategory','standart grouped');
        $i = 0;
        foreach($categoryDataW as $key=>$value){
            $i++;
            $form->setDataForm(array(
                        'title_'.$i=>
                            array('required'=>true,'validators'=>array('regex'=>'numbers_letters',),'setLabel'=>'название категории'),
//                        'id_'.$value['id']=>array(,'required'=>true,'validators'=>array('regex'=>'numbers_letters')),
                        'id_'.$i=>
                            array('required'=>true,'typeInput'=>'hidden','validators'=>array('regex'=>'numbers'),'filters'=>array('trim','int','tag'))


            ));

        }
        $this->request = $this->getRequest();

        if($this->request->isPost()){

            $form->setData($this->request->getPost());
            if($form->isValid()){

                $this->validData = $form->getData();
                $i=0;
                foreach($this->validData as $key=>$value){
                    $i++;
                    \Royal\Models\CategoryPagesModel::model()
                        ->setAttributes(array('title'=>$value['title_'.$i],
                            'id'=>$value['id_'.$i]))
                        ->save();
                }
            }
        }else{

//            $form->setData($categoryDataW);

        }
        return new ViewModel(array(
            'form'=>$form,
            'categoryDataW'=>$categoryDataW
        ));
    }
}
