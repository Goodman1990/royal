<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\TableGateway\Feature;

class IndexController extends AbstractActionController
{
	public $adapter;

    public function indexAction()
    {
	$feature = new Feature\GlobalAdapterFeature();
        $this->adapter = $feature->getStaticAdapter();
        $courseListByUser =\Application\Models\UsersModel::model()->findByAttributes(array('id' =>1));
        print_r($courseListByUser);
        exit();
        return new ViewModel();
    }
}
