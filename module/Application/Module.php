<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Page\Model\Page;
use Zend\Db\TableGateway\Feature;
use Zend\Db\ResultSet\ResultSet;


class Module implements ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');

        // $translator = $serviceManager->get('translator');

        Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);

       

    }   

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
			  'ActiveRecord' =>  __DIR__ . '/../../vendor/ActiveRecord/lib/ActiveRecord',
                ),
            ),
        );
    }


    public function getServiceConfig() {
        return array();       
    }

}
