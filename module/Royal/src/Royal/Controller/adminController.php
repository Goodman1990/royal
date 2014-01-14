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

    public function indexAction()
    {

        $form = new formGenerate('auth','auth',array(

        ));

        return new ViewModel(array(

            )
        );
    }

    private function renderMenuTop(){}
    private function renderMenuBottom(){}
    private function renderMenuRight(){}
}
