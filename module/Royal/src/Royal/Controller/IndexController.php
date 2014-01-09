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
use Zend\Navigation\Navigation;
use Royal\helpers\generalHelper;


class IndexController extends AbstractActionController
{
	public $adapter;

    public function indexAction()
    {

//            echo 123;
//            exit;

        $generalHelper = new generalHelper();
        $navigationInfo = \Royal\Models\CategoryPagesModel::model()->getAll();
        
        
        foreach ($navigationInfo as $key ) {
        
            $navigation['navigation']['default'][] =  array(
                        'label' => $key['title'],
                        'route' => 'Royal',
                        'action' => 'index',
                        'param'=>array('id_page'=>$key['id'].'_'.$generalHelper->transliteration($key['title']))
                    );
        }

//        print_r ($navigation);
//        exit;
//	    $feature = new Feature\GlobalAdapterFeature();
//        $this->adapter = $feature->getStaticAdapter();
        // $courseListByUser =\Royal\Models\CategoryPagesModel::model(array('asArray'=>true))->findByAttributes(array('id' =>1));
        // $dd =\Royal\Models\CategoryPagesModel::model()->getAll();
        // print_r($dd);
        // exit;
        return new ViewModel(array(

            )
        );
    }

    private function renderMenuTop(){}
    private function renderMenuBottom(){}
    private function renderMenuRight(){}
}
